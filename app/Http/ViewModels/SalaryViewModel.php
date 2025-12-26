<?php

namespace App\Http\ViewModels;

use App\Http\Forms\SalaryForm;
use App\Http\Requests\FormRequestInterface;
use App\Http\Requests\SalaryReportFormRequest;
use App\Libraries\Payroll\PayrollCalculator;
use App\Managers\Form\FormBuilder;
use App\Models\Attendance;
use App\Models\CalendarEvent;
use App\Models\ModelInterface;
use App\Repositories\Eloquent\AttendanceRepository;
use App\Repositories\Eloquent\CalendarEventRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\SettingsRepository;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use O2System\Spl\Datastructures\SplArrayObject;


class SalaryViewModel extends ViewModelBase {
	private ?Request $request;

	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'salary';
		$this->routeKey = 'salary';
	}

	public function request() {
		return $this->request;
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['salary' => $model->id]));

		return $this;
	}

	public function createReportForm(string $method, string $route, array $options = []): ViewModelBase {
		$this->form = $this->formBuilder->create(SalaryForm::class, $options);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route));

		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		// TODO: Implement update() method.
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
	}

	public function new(FormRequestInterface $request): mixed {
		// TODO: Implement new() method.
	}

	public function setRequest(Request $request) {
		$this->request = $request;

		return $this;
	}

	public function reports(SalaryReportFormRequest $request, EmployeeRepository $employeeRepository, SettingsRepository $settingsRepository,
		AttendanceRepository $attendanceRepository,
		CalendarEventRepository $calendarEventRepository
	) {
		$self = $this;
		$this->setRepository($employeeRepository);
		list($offset, $limit, $sort, $order, $search, $date, $start, $end) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request);
		$cutoff = $settingsRepository->findOneBySectionAndKey('attendance', 'cutoff');
		$current = clone($start);
	    [$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = self::getWorkingMonth($settingsRepository, $current);
		
		$columns = $this->getDefaultColumns();
		$results = $query->with(['currency:code,name,symbol'])
		                 ->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();
		$workingDays = count($this->workingDays($settingsRepository));
						 
		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use (
			$self, $settingsRepository, $attendanceRepository, $calendarEventRepository, $current, $workingDays, $prev, $next,
		) {
			
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self, $settingsRepository, $attendanceRepository, $calendarEventRepository, $current, $workingDays, $prev, $next) {
					$payroll = $self->payrollCalc($result, $settingsRepository, $attendanceRepository, $calendarEventRepository, $prev, $next);
					$result['name'] = $self->createLink($result['name'], route('employee.show', ['employee' => $result['id']]));
					$result['nik'] = $self->createLink($result['nik'], route('employee.show', ['employee' => $result['id']]));
					$result['payroll'] = $payroll;
					$result['start'] = $prev->format('Y-m-d');
					$result['end'] = $next->format('Y-m-d');
					$i++;
					
					return $result;
				});
			}
			
			return $item;
		});
	}

	public function payrollCalc(array $employee, SettingsRepository $settingsRepository, AttendanceRepository $attendanceRepository,
		CalendarEventRepository $calendarEventRepository, \DateTime $prev, \DateTime $next,
	) {

		$currentDate = new \DateTime();
		$endDate = $prev;

		if ($currentDate <= $endDate) {
			return null;
		}

		$payrollCalculator = new PayrollCalculator();
		$payrollCalculator->method = PayrollCalculator::GROSS_CALCULATION;
		$payrollCalculator->taxNumber = PayrollCalculator::PPH21;
		$payrollCalculator->employee->permanentStatus = $employee['permanent_status'];
		$payrollCalculator->employee->employeeGuarantee = $employee['employee_guarantee'];
		$payrollCalculator->employee->maritalStatus = $employee['marital_status'];
		$payrollCalculator->employee->hasNPWP = $employee['has_npwp'];
		$payrollCalculator->employee->numOfDependentsFamily = $employee['num_of_dependents_family'];
		$payrollCalculator->employee->earnings->base = $employee['basic_salary'];
		$payrollCalculator->employee->earnings->fixedAllowance = round($employee['functional_allowance'] +
		                                                         $employee['transport_allowance'] +
		                                                         $employee['meal_allowances'] +
		                                                         $employee['other_allowance']);
		
		$att = $this->workDays($employee, $settingsRepository, $attendanceRepository);
		$workingDays = $this->workingDays($settingsRepository);
		$attDetail = $this->attendanceDetail($employee, $settingsRepository, $calendarEventRepository);
		$overtime = [];
		
		foreach ($attDetail as $detail) {
			if (!empty($detail['total']))
				$overtime[] = [
					'at'       => $detail['at'],
					'start'    => $detail['start'],
					'end'      => $detail['end'],
					'overtime' => $detail['overtime'],
				];
		}
		$totalovertime = $this->totalHours($overtime);

		$payrollCalculator->employee->presences->workDays = count($workingDays);          // $att->present ?? 0;           // jumlah hari masuk kerja
		$payrollCalculator->employee->presences->overtime = $totalovertime['hours'] ?? 0; // perhitungan jumlah lembur dalam satuan jam
		$payrollCalculator->employee->presences->overtimeHours = $totalovertime['hours'] ?? 0;
		$payrollCalculator->employee->presences->overtimeMinutes = $totalovertime['minutes'] ?? 0;
		$payrollCalculator->employee->presences->latetime = 0;                            // perhitungan jumlah keterlambatan dalam satuan jam
		$payrollCalculator->employee->presences->travelDays = $att->business_trip ?? 0;   // perhitungan jumlah hari kepergian dinas
		$payrollCalculator->employee->presences->indisposedDays = $att->sick ?? 0;        // perhitungan jumlah hari sakit yang telah memiliki surat dokter
		$payrollCalculator->employee->presences->absentDays = $att->absent ?? 0;          // perhitungan jumlah hari alpha
        $payrollCalculator->employee->presences->splitShifts = 0;                         // perhitungan jumlah split shift		
		// Set data tunjangan karyawan di luar tunjangan BPJS Kesehatan dan Ketenagakerjaan
		// $payrollCalculator->employee->allowances->offsetSet('other_allowance', $employee->other_allowance);
		// $payrollCalculator->employee->allowances->offsetSet('meal_allowances', $employee->meal_allowances);
		// $payrollCalculator->employee->allowances->offsetSet('transport_allowance', $employee->transport_allowance);
		// $payrollCalculator->employee->allowances->offsetSet('functional_allowance', $employee->functional_allowance);

		$payrollCalculator->provisions->state->overtimeRegulationCalculation = false;          // Jika false maka akan dihitung sesuai kebijakan perusahaan
		$payrollCalculator->provisions->state->provinceMinimumWage = 6000000;                  // Ketentuan UMP sesuai propinsi lokasi perusahaan

		// Set data ketentuan perusahaan
		$payrollCalculator->provisions->company->numOfWorkingDays = count($workingDays);       // Jumlah hari kerja dalam satu bulan
		$payrollCalculator->provisions->company->numOfWorkingHours = 8;                        // Jumlah jam kerja dalam satu hari
		$payrollCalculator->provisions->company->overtimeRate = 10000;                         // Rate lembur perjam
		$payrollCalculator->provisions->company->calculateOvertime = false;                    // Apakah perusahaan menghitung lembur
		$payrollCalculator->provisions->company->calculateSplitShifts = false;                 // Apakah perusahan menghitung split shifts
		$payrollCalculator->provisions->company->splitShiftsRate = 25000;                      // Rate Split Shift perusahaan
		$payrollCalculator->provisions->company->calculateBPJSKesehatan = true;                // Apakah perusahaan menyediakan BPJS Kesehatan / tidak untuk orang
		// tersebut
		// Apakah perusahaan menyediakan BPJS Ketenagakerjaan / tidak untuk orang tersebut
		$payrollCalculator->provisions->company->JKK = true;                                   // Jaminan Kecelakaan Kerja
		$payrollCalculator->provisions->company->fixed_JKK = true;                             // Jaminan Kecelakaan Kerja
		$payrollCalculator->provisions->company->JKM = true;                                   // Jaminan Kematian
		$payrollCalculator->provisions->company->JHT = true;                                   // Jaminan Hari Tua
		$payrollCalculator->provisions->company->JIP = true;                                   // Jaminan Pensiun
		$payrollCalculator->provisions->company->riskGrade = 1;                                // Golongan resiko ketenagakerjaan, umumnya 2
		$payrollCalculator->provisions->company->absentPenalty = 0;                            // Perhitungan nilai potongan gaji/hari sebagai penalty.
		$payrollCalculator->provisions->company->latetimePenalty = 0;                          // Perhitungan nilai keterlambatan sebagai penalty.

		$payrollCalculator->getCalculation();
		$payroll = $payrollCalculator;
		$currencyCode = $employee['currency']['symbol'];

		$bruto_monthly = ($payroll->result->earnings->baseTotal +  $payroll->result->allowances->getSum() + $payroll->company->allowances->BPJSKesehatan + $payroll->company->allowances->JKM + $payroll->company->allowances->JKK);
		$bruto_annual = $bruto_monthly * 12;
		$thr = 0;
		$position_tax = 0;

		// Biaya Jabatan (5% BRUTO per BULAN(Max 6juta per tahun))
		if (($bruto_annual + $thr) * 0.05 > 6000000) {
			$position_tax = 500000;
		} else {
			$position_tax = ($bruto_monthly + $thr / 12) * 0.05;
		}

		// Gaji Netto per tahun
		$nett_annual = round($bruto_monthly + $thr / 12 - $position_tax - $payroll->result->deductions->JIP - $payroll->result->deductions->JHT) * 12;

		// PKP
		$roundedPkp = $nett_annual - $payroll->result->taxable->ptkp->amount;
		$pkp = 0;
		if ($roundedPkp >= 0) {
			$pkp = floor(($nett_annual - $payroll->result->taxable->ptkp->amount) / 1000) * 1000;
		} else {
			$pkp = ceil(($nett_annual - $payroll->result->taxable->ptkp->amount) / 1000) * 1000;
		}

		// PPh 21
		$pph21_annual = $this->getTaxPph21($pkp);
		$pph21_monthly = round($pph21_annual / 12);

		// Take Home Pay
		$takeHomePay = $payroll->result->earnings->baseTotal + $payroll->result->allowances->getSum() - $payroll->result->deductions->BPJSKesehatan - $payroll->result->deductions->JIP - $payroll->result->deductions->JHT - $pph21_monthly;
		
		return [
			'earnings'   => [
				'base'      => $this->moneyFormat($payroll->result->earnings->base, $currencyCode, 'Base Salary'),
				'allowance' => [
					'functional' => $this->moneyFormat($employee['functional_allowance'], $currencyCode, 'Functional Allowance'),
					'transport'  => $this->moneyFormat($employee['transport_allowance'], $currencyCode, 'Transport Allowance'),
					'meal'       => $this->moneyFormat($employee['meal_allowances'], $currencyCode, 'Mel Allowance'),
				]
			],
			'deductions' => [
				'bpjs_kesehatan' => $this->moneyFormat($payroll->company->allowances->BPJSKesehatan, $currencyCode, 'BPJS Kesehatan'),
				'jkm' 			 => $this->moneyFormat($payroll->company->allowances->JKM, $currencyCode, 'JKM (Jaminan Kematian)'),
				'jkk' 			 => $this->moneyFormat($payroll->company->allowances->JKK, $currencyCode, 'JKK (Jaminan Kecelakaan Kerja)'),
				'jht'            => $this->moneyFormat($payroll->result->deductions->JHT, $currencyCode, 'JHT (Jaminan Hari Tua)'),
				'jip'            => $this->moneyFormat($payroll->result->deductions->JIP, $currencyCode, 'JIP (Jaminan Pensiun)'),
				'position_tax'   => $this->moneyFormat($position_tax, $currencyCode, 'Pajak Jabatan'),
				'pph21_annual'   => $this->moneyFormat($pph21_annual, $currencyCode, 'PPH 21 Per Tahun'),
				'pph21_monthly'  => $this->moneyFormat($pph21_monthly, $currencyCode, 'PPH 21 Per Bulan'),
				'presences'      => $this->moneyFormat($payroll->result->deductions->presence ?? 0, $currencyCode, 'Potongan Kehadiran'),
			],
			'ptkp'       => [
				'pkp' 	 => $this->moneyFormat($pkp, $currencyCode, 'PKP'),
				'status' => $payroll->result->taxable->ptkp->status,
				'amount' => $this->moneyFormat($payroll->result->taxable->ptkp->amount, $currencyCode, 'PTKP'),
			],
			'total'      => [
				'bruto_monthly'	=> $this->moneyFormat($bruto_monthly, $currencyCode, 'Bruto Monthly'),
				'nett_annual'	=> $this->moneyFormat($nett_annual, $currencyCode, 'Nett Annually'),
				'earning'       => $this->moneyFormat($payroll->result->earnings->baseTotal, $currencyCode, 'Total Earning'),
				'deduction'     => $this->moneyFormat($payroll->result->deductions->getSum(), $currencyCode, 'Total Deduction'),
				'take_home_pay' => $this->moneyFormat($takeHomePay, $currencyCode, 'Take Home Pay'),
			],
		];
	}

	public function getTaxPph21(int $pkp): float|int {
		if ($pkp <= 0) {
			return 0;
		}

		// 60.000.000 (5%)
		if ($pkp <= 60000000) {
			return $pkp * 0.05;
		}

		// 250.000.000 (15%)
		if ($pkp <= 250000000) {
			return 3000000 + ($pkp - 60000000) * 0.15;
		}

		// 500.000.000 (25%)
		if ($pkp <= 500000000) {
			return 31500000 + ($pkp - 250000000) * 0.25;
		}

		// 95.000.000 (30%)
		return 94000000 + ($pkp - 500000000) * 0.3;
	}

	private function workDays(array $employee, SettingsRepository $settingsRepository, AttendanceRepository $attendanceRepository) {
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, new \DateTime());

		// @formatter:off
		$query = Attendance::with(['employee:id,name', 'reason:id,name', 'annualLeave:id,no,year,used_at'])
		                   ->groupBy('employee_id')
		                   ->select(
			                   DB::raw('employee_id'),
			                   DB::raw('(SELECT name FROM employees b WHERE b.id = attendances.employee_id) AS employee_name'),
			                   DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("'.$prev->format('Y-m-d').'") AND DATE(b.at) <= DATE("'.$next->format('Y-m-d').'") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 1 GROUP BY attendance_reason_id) AS present'),
			                   DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("'.$prev->format('Y-m-d').'") AND DATE(b.at) <= DATE("'.$next->format('Y-m-d').'") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 2 GROUP BY attendance_reason_id) AS sick'),
			                   DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("'.$prev->format('Y-m-d').'") AND DATE(b.at) <= DATE("'.$next->format('Y-m-d').'") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 3 GROUP BY attendance_reason_id) AS business_trip'),
			                   DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("'.$prev->format('Y-m-d').'") AND DATE(b.at) <= DATE("'.$next->format('Y-m-d').'") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 4 GROUP BY attendance_reason_id) AS permit'),
			                   DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("'.$prev->format('Y-m-d').'") AND DATE(b.at) <= DATE("'.$next->format('Y-m-d').'") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 5 GROUP BY attendance_reason_id) AS absent'),
			                   DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("'.$prev->format('Y-m-d').'") AND DATE(b.at) <= DATE("'.$next->format('Y-m-d').'") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 6 GROUP BY attendance_reason_id) AS annual_leave'),
		                   )
		                   ->where('employee_id', '=', $employee['id']);
		// print_r($query->toSql());
		// @formatter:on

		return $query->first();
	}

	public static function getWorkingMonth(SettingsRepository $settingsRepository, \DateTime $start) {
		$cutoff = $settingsRepository->findOneBySectionAndKey('attendance', 'cutoff');
		$now = clone($start);

		if ($cutoff->value !== 'end_of_month') {
			//if ((int)date('d') < (int)$cutoff->value)
			$now = $now->sub(new \DateInterval('P1M'));
		}

		$year = $now->format('Y');
		$month = $now->format('m');
		$cutoffDateStart = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value + 1, 2, '0', STR_PAD_LEFT);
		$cutoffDateEnd = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value, 2, '0', STR_PAD_LEFT);
		$prev = new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateStart)));
		$next = (new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateEnd))))->add(new \DateInterval('P1M'));
		if ($cutoff->value === 'end_of_month') $next = $next->sub(new \DateInterval('P1D'));

		return [$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd];
	}

	public function workingDays(SettingsRepository $settingsRepository) {
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, new \DateTime());

		$d = clone($prev);
		$events = $this->nationalEvents(clone($d), clone($next));
		$dates = [];
		while ($d <= $next) {
			if ($d->format('N') < 6 && !in_array($d, $events)) $dates[] = clone($d);
			$d = $d->add(new \DateInterval('P1D'));
		}

		return $dates;
	}

	private function nationalEvents(\DateTime $start, \DateTime $end) {
		$months = [];
		$years = [];
		$s = (int)$start->format('n');
		$sy = (int)$start->format('Y');
		$e = (int)$end->format('n');

		while (1) {
			$months[] = $s;
			$years[] = $sy;
			$s++;
			if ($s > 12) {
				$s = 1;
				$sy++;
			}

			if ($s == $e) {
				$months[] = $s;
				$years[] = $sy;
				break;
			}
		}

		$map = function ($event) use ($start, $end, $years, $months) {
			// Fix the year on recurring events
			if ($event['recurring']) {
				$d = new \DateTime($event['start_date']);
				$m = $d->format('n');
				$index = array_search($m, $months);

				if ($index !== false) {
					$event['start_date'] = sprintf("%s-%s-%s", $years[$index], $d->format('m'), $d->format('d'));
				}
			}

			return new \DateTime($event['start_date']);
		};

		$event = CalendarEvent::whereDate('start_date', '>=', $start)
		                      ->whereDate('start_date', '<=', $end)
		                      ->where('recurring', '=', false)
		                      ->get()->map($map)->toArray();
		$recurring = CalendarEvent::whereRaw(sprintf('MONTH(start_date) IN (%s)', implode(',', $months)))
		                          ->where('recurring', '=', true)
		                          ->get()->map($map)->toArray();

		return array_merge($event, $recurring);
	}

	private function attendanceDetail(array $employee, SettingsRepository $settingsRepository, CalendarEventRepository $calendarEventRepository) {
		$self = $this;
		$cutoff = $settingsRepository->findOneBySectionAndKey('attendance', 'cutoff');
		$start = new \DateTime();
		$imonth = $start->format('n');
		if ($cutoff->value !== 'end_of_month') {
			if ((int)$start->format('d') < (int)$cutoff->value)
				$start = $start->sub(new \DateInterval('P1M'));
		}
		$year = $start->format('Y');
		$month = $start->format('m');
		$imonthprev = $start->format('n');
		$start = 1;
		$cutoffDateStart = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value + 1, 2, '0', STR_PAD_LEFT);
		$cutoffDateEnd = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value, 2, '0', STR_PAD_LEFT);
		$currentDate = new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateStart)));
		$next = (new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateEnd))))->add(new \DateInterval('P1M'));
		if ($cutoff->value === 'end_of_month') $next = $next->sub(new \DateInterval('P1D'));

		$days = [];
		$results = Attendance::with(['employee:id,name', 'reason:id,name', 'annualLeave:id,no,year,used_at'])
		                     ->where('employee_id', '=', $employee['id'])
		                     ->whereDate('at', '>=', $currentDate)
		                     ->whereDate('at', '<=', $next)
		                     ->get()
		                     ->toArray();
		$reasons = ['present' => 1, 'sick' => 2, 'business_trip' => 3, 'permit' => 4, 'absent' => 5, 'annual_leave' => 6];

		///
		$events = CalendarEvent::whereMonth('start_date', '=', $imonthprev)
		                       ->where('recurring', '=', 1)
		                       ->get()
		                       ->map(function ($item) use ($year) {
			                       $item['start_date'] = new \DateTime($year . '-' . (new \DateTime($item['start_date']))->format('m-d'));

			                       return $item;
		                       });
		$events = $events->merge(CalendarEvent::whereMonth('start_date', '=', $imonth)
		                                      ->where('recurring', '=', 1)
		                                      ->get()
		                                      ->map(function ($item) use ($year) {
			                                      $item['start_date'] = new \DateTime($year . '-' . (new \DateTime($item['start_date']))->format('m-d'));

			                                      return $item;
		                                      }));
		$events = $events->merge(CalendarEvent::whereYear('start_date', '=', $currentDate->format('Y'))
		                                      ->whereMonth('start_date', '=', $currentDate->format('n'))
		                                      ->whereDay('start_date', '=', $currentDate->format('j'))
		                                      ->where('recurring', '=', 0)
		                                      ->get());
		if ($next->format('n') > $currentDate->format('n')) {
			$events = $events->merge(CalendarEvent::whereYear('start_date', '=', $next->format('Y'))
			                                      ->whereMonth('start_date', '=', $next->format('n'))
			                                      ->whereDay('start_date', '=', $next->format('j'))
			                                      ->where('recurring', '=', 0)
			                                      ->get());
		}
		///

		for ($day = $start; $currentDate <= $next; $day++) {
			$data = collect($results)->filter(function ($item) use ($currentDate) {
				return $currentDate->format('Y-m-d') == (new \DateTime($item['at']))->format('Y-m-d');
			})->first();

			$att = collect($reasons)->map(function ($item, $key) use ($data) {
				if (empty($data)) return null;

				return $item == $data['reason']['id'] ? '<i class="fad fa-check"></i>' : null;
			})->toArray();

			$event = $events->filter(function ($item) use ($currentDate) {
				return $item['start_date'] == $currentDate;
			})->first();

			$detail = null;
			if (!empty($data)) {
				$detail = $data['detail'] ?? null;
			}
			if ($event) $detail = $event['title'];

			$d = array_merge([
				'no'       => count($days) + 1,
				'date'     => $currentDate->format('l, d F Y'),
				'at'       => $currentDate->format('Y-m-d'),
				'start'    => !empty($data) ? $data['start'] : null,
				'end'      => !empty($data) ? $data['end'] : null,
				'overtime' => !empty($data) ? $data['overtime'] : null,
				'total'    => 0,
				'remark'   => $detail,
				'event'    => $event ? true : false,
				'weekend'  => in_array($currentDate->format('w'), [0, 6]),
			], $att);

			if (!empty($d['start']) || !empty($d['end']) || !empty($d['overtime'])) {
				$day1hours = new \DateTime(sprintf("%s %s", (new \DateTime($data['at']))->format('Y-m-d'), $d['start']));

				if (empty($d['end'])) {
					$d['total'] = "00:00";
					$day2hours = clone($day1hours);
				}
				else {
					$day2hours = new \DateTime(sprintf("%s %s", (new \DateTime($data['at']))->format('Y-m-d'), $d['end']));
					$d['total'] = $day2hours->diff($day1hours)->format('%H:%I');
				}

				if (!empty($d['overtime'])) {
					$overtime = (new \DateTime($d['overtime']))->diff($day2hours)->format('%H:%I');
					$parts = explode(':', $d['total']);
					$overtime = (new \DateTime($overtime))->add(new \DateInterval('PT' . intval($parts[0]) . 'H' . intval($parts[1]) . 'M'));
					$d['total'] = $overtime->format('H:i');
				}

				if ($d['weekend'] && empty($d['remark'])) $d['remark'] = "Overtime";
			}

			$days[] = $d;

			$prev = (new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateStart))));
			$currentDate = $prev->add(new \DateInterval('P' . $day . 'D'));
		}

		return $days;
	}

	private function totalHours(array $hourMin) {
		$hours = 0;
		$mins = 0;

		foreach ($hourMin as $val) {
			if (!empty($val['overtime'])) {
				$hours1 = new \DateTime(sprintf("%s %s", (new \DateTime($val['at']))->format('Y-m-d'), $val['end']));
				$hours2 = new \DateTime(sprintf("%s %s", (new \DateTime($val['at']))->format('Y-m-d'), $val['overtime']));
				$overtime = $hours1->diff($hours2)->format('%H:%I');

				$explodeHoursMins = explode(':', $overtime);
				$hours += (int)$explodeHoursMins[0];
				$mins += (int)$explodeHoursMins[1];
			}
		}

		$minToHours = date('H:i', mktime(0, $mins)); //Calculate Hours From Minutes
		$explodeMinToHours = explode(':', $minToHours);
		$hours += (int)$explodeMinToHours[0];
		$finalMinutes = (int)$explodeMinToHours[1];

		return ['hours' => (int)$hours, 'minutes' => (int)$finalMinutes];
	}
}
