<?php
/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   EmployeeViewModel.php
 * @date   2021-03-17 20:2:27
 */

namespace App\Http\ViewModels;

use App\Http\Forms\EmployeeForm;
use App\Http\Requests\FormRequestInterface;
use App\Libraries\Payroll\PayrollCalculator;
use App\Managers\Form\FormBuilder;
use App\Models\Attendance;
use App\Models\CalendarEvent;
use App\Models\Employee;
use App\Models\Fingerprint;
use App\Models\ModelInterface;
use App\Repositories\Eloquent\AttendanceRepository;
use App\Repositories\Eloquent\CalendarEventRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\SettingsRepository;
use App\Repositories\EloquentRepositoryInterface;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class EmployeeViewModel extends ViewModelBase {
	public $payroll;

	/**
	 * EmployeeViewModel constructor.
	 *
	 * @param \App\Repositories\EloquentRepositoryInterface $repository
	 * @param \App\Managers\Form\FormBuilder|null $formBuilder
	 *
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'employee';
		$this->routeKey = 'employee';
		$this->form = $this->formBuilder->create(EmployeeForm::class);
	}

	/**
	 * @inheritDoc
	 */
	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['employee' => $model->id]));

		return $this;
	}

	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		$results = $query->with(['country:iso,name', 'state:id,name', 'city:id,name', 'district:id,name', 'village:id,name', 'position:id,name'])
		                 ->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					// $result['profile_photo_path'] =
					// 	'<div class="avatar avatar-2xl"><img class="rounded-circle w-100" src="' . $result['profile_photo_path'] . '" /></div>';
					// $result['age'] = (new DateTime())->diff($result['birth_date'])->y;
                    $result['gender'] = $result['gender_id'] != null ? ($result['gender_id'] == 1 ? "Male" : "Female") : "No data";
					$result['effective_since'] = $result['effective_since'] != null ? $result['effective_since']->format('Y-m-d') : "No data";

					return $self->addDefaultListActions($result);
				});
			}

			return $item;
		});
	}

	/**
	 * @inheritDoc
	 */
	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		if ($fields->has('profile_photo_path'))
			$fields->offsetSet('profile_photo_path', $this->convertImage($request, 'profile_photo_path'));

		$fields->offsetSet('has_npwp', $this->toBool($fields->get('has_npwp')));
		$fields->offsetSet('permanent_status', $this->toBool($fields->get('permanent_status')));
		$fields->offsetSet('employee_guarantee', $this->toBool($fields->get('employee_guarantee')));

		$ret = $model->update($fields->toArray());

		return $ret;
	}

	/**
	 * @inheritDoc
	 */
	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
        $this->form->setRequest($request);
        $this->form->redirectIfNotValid();

        if(!Employee::find($model->id)->forceDelete()){
            $request->session()->flash('message', "Failed to delete <strong>{$model->name}</strong>");
            $request->session()->flash('alert', "danger");
        }else{
            $request->session()->flash('message', "Successfully delete!</strong>.");
            $request->session()->flash('alert', "success");
        }
        return redirect(route('employee.index'));
	}

	/**
	 * @inheritDoc
	 */
	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		if ($fields->has('profile_photo_path'))
			$fields->offsetSet('profile_photo_path', $this->convertImage($request, 'profile_photo_path'));

		$emp = $fields->toArray();
		unset($emp['finger']);
		unset($emp['finger_size']);
		unset($emp['finger_index']);

		$employee = new Employee($emp);
		$ret = $employee->save();

		$annuals = [];
		for ($i = 0; $i < 14; $i++) {
			$annuals[] = [
				'no'   => sprintf('val-%02d-%s', $i + 1, date('Y')),
				'year' => date('Y'),
			];
		}
		$employee->annualLeaves()->createMany($annuals);

		// $finger = Fingerprint::where('pin', '=', $fields->get('pin'))->first();
		// if ($finger === null) {
		// 	$finger = new Fingerprint([
		// 		'pin'       => $fields->get('pin'),
		// 		'template'  => $fields->get('finger'),
		// 		'valid'     => true,
		// 		'finger_id' => $fields->get('finger_index'),
		// 		'size'      => $fields->get('finger_size'),
		// 	]);
		// 	$finger->save();
		// }

		return $ret ? $employee : false;
	}

	public function select2List(Request $request): Collection {
		$search = $request->get('search', null);
		$results = collect([]);
		$items = null;

		if (!empty($search)) {
			$items = Employee::search($search);
		}
		else {
			$items = Employee::query();
		}

		$items = $items->orderBy('name')->get();
		$results->offsetSet('results',
			$items->count() ? $items : [
				['id' => 0, 'text' => 'Nothing here'],
			]);

		return $results;
	}

	public function selectAvailableEmployee(Request $request, EmployeeRepository $employeeRepository) {
		list($offset, $limit, $sort, $order, $search, $date, $start, $end) = $this->getDefaultRequestParam($request);
		$search = $request->get('search', null);
		$results = collect([])->filter();
		$items = null;

		if (!empty($search)) {
			$items = $this->repository
				->with([
					'attendance' => function (HasMany $attendance) use ($start) {
						return $attendance->whereRaw('DATE(at) = DATE("' . $start->format('Y-m-d') . '")');
					},
				])
				->select('id', 'name')
				->where('name', 'LIKE', "%$search%")
				->get();
		}
		else {
			$items = $this->repository
				->with([
					'attendance' => function (HasMany $attendance) use ($start) {
						return $attendance->whereRaw('DATE(at) = DATE("' . $start->format('Y-m-d') . '")');
					},
				])
				->select('id', 'name')
				->get();
		}

		$employees = [];
		$items
			->map(function ($employee) {
				if (is_previous_route('attendance.edit') || is_current_route('attendance.edit')) {
					return ['id' => $employee['id'], 'name' => $employee['name']];
				}

				return count($employee['attendance']) ? null : ['id' => $employee['id'], 'name' => $employee['name']];
			})
			->filter(function ($employee) {
				return !empty($employee);
			})->each(function ($employee) use (&$employees) {
				$employees[] = $employee;
			});

		$results->offsetSet('results', $employees);

		return $results;
	}

	public function payrollCalc(Request $request, SettingsRepository $settingsRepository, AttendanceRepository $attendanceRepository,
		CalendarEventRepository $calendarEventRepository
	) {
		/**
		 * @var $employee Employee
		 */
		$employee = $this->model();
		$payrollCalculator = new PayrollCalculator();
		$payrollCalculator->method = PayrollCalculator::GROSS_CALCULATION;
		$payrollCalculator->taxNumber = PayrollCalculator::PPH21;
		$payrollCalculator->employee->permanentStatus = $employee->permanent_status;
		// $payrollCalculator->employee->employeeGuarantee = $employee->employee_guarantee;
		$payrollCalculator->employee->maritalStatus = $employee->marital_status;
		$payrollCalculator->employee->hasNPWP = $employee->has_npwp;
		$payrollCalculator->employee->numOfDependentsFamily = $employee->num_of_dependents_family;
		$payrollCalculator->employee->earnings->base = $employee->basic_salary;
		$payrollCalculator->employee->earnings->fixedAllowance = (int)($employee->functional_allowance + $employee->transport_allowance + $employee->meal_allowances + $employee->other_allowance);

		$att = $this->workDays($employee, $settingsRepository, $attendanceRepository);
		$workingDays = $this->workingDays($settingsRepository);
		$attDetail = $this->attendanceDetail($request, $employee, $attendanceRepository, $settingsRepository, $calendarEventRepository);
		$overtime = [];
		$overtimes = 0;
		$totalPresence = 0;
		$totalWorkDays = 0;
		foreach ($attDetail as $detail) {
			$totalPresence += !empty($detail['present']) ? 1 : 0;
			$totalPresence += !empty($detail['business_trip']) ? 1 : 0;
			$totalWorkDays += (int)($detail['present'] ?? 0) + (int)($detail['sick'] ?? 0) + (int)($detail['business_trip'] ?? 0) + (int)($detail['permit'] ?? 0) + (int)($detail['annual_leave'] ?? 0);

			if (!empty($detail['total'])) {
				$overtime[] = [
					'at'       => \DateTime::createFromFormat('l, d F Y', $detail['date'])->format('Y-m-d'),
					'start'    => $detail['start'],
					'end'      => $detail['end'],
					'overtime' => $detail['overtime'],
				];
				if (Carbon::parse($detail['date'])->isWeekend()) ++$overtimes;
			}
		}
		$totalovertime = $this->totalHours($overtime);
		// dd($attDetail);

		//$payrollCalculator->employee->presences->workDays = $totalPresence;                         // jumlah hari masuk kerja
		$payrollCalculator->employee->presences->workDays = $att->present ?? 0;                    // jumlah hari masuk kerja
		$payrollCalculator->employee->presences->overtimeDays = $overtimes ?? 0;           // perhitungan jumlah lembur dalam satuan jam
		$payrollCalculator->employee->presences->overtime = $totalovertime['hours'] ?? 0;           // perhitungan jumlah lembur dalam satuan jam
		$payrollCalculator->employee->presences->overtimeHours = $totalovertime['hours'] ?? 0;
		$payrollCalculator->employee->presences->overtimeMinutes = $totalovertime['minutes'] ?? 0;
		$payrollCalculator->employee->presences->latetime = 0;                                      // perhitungan jumlah keterlambatan dalam satuan jam
		$payrollCalculator->employee->presences->travelDays = $att->business_trip ?? 0;             // perhitungan jumlah hari kepergian dinas
		$payrollCalculator->employee->presences->indisposedDays = $att->sick ?? 0;                  // perhitungan jumlah hari sakit yang telah memiliki surat dokter
		$payrollCalculator->employee->presences->absentDays =  (count($workingDays) - ($att->present ?? 0)) ?? 0;                    // perhitungan jumlah hari alpha
		$payrollCalculator->employee->presences->splitShifts = 0;                                   // perhitungan jumlah split shift
		$payrollCalculator->employee->presences->rate = $employee->attendance_premium ?? 0;
		$payrollCalculator->employee->presences->overtimeRate = $employee->overtime ?? 0;
		//$payrollCalculator->employee->presences->absentDays = count($workingDays) - $totalWorkDays; // perhitungan jumlah split shift
		// Set data tunjangan karyawan di luar tunjangan BPJS Kesehatan dan Ketenagakerjaan
		$payrollCalculator->employee->allowances->offsetSet('meal', $employee->meal_allowances ?? 0);
		$payrollCalculator->employee->allowances->offsetSet('transport', $employee->transport_allowance ?? 0);
		$payrollCalculator->employee->allowances->offsetSet('functional', $employee->functional_allowance ?? 0);
		$payrollCalculator->employee->allowances->offsetSet('other', $employee->other_allowance ?? 0);

		$payrollCalculator->provisions->state->overtimeRegulationCalculation = false;     // Jika false maka akan dihitung sesuai kebijakan perusahaan
		$payrollCalculator->provisions->state->provinceMinimumWage = 6000000;             // Ketentuan UMP sesuai propinsi lokasi perusahaan

		// Set data ketentuan perusahaan
		$payrollCalculator->provisions->company->numOfWorkingDays = count($workingDays);  // Jumlah hari kerja dalam satu bulan
		$payrollCalculator->provisions->company->numOfWorkingHours = 8;                   // Jumlah jam kerja dalam satu hari
		$payrollCalculator->provisions->company->overtimeRate = $employee->overtime ?? 0;      // Rate lembur perjam
		$payrollCalculator->provisions->company->calculateOvertime = false;               // Apakah perusahaan menghitung lembur
		$payrollCalculator->provisions->company->calculateSplitShifts = false;            // Apakah perusahan menghitung split shifts
		$payrollCalculator->provisions->company->splitShiftsRate = 25000;                 // Rate Split Shift perusahaan
		$payrollCalculator->provisions->company->calculateBPJSKesehatan = true;           // Apakah perusahaan menyediakan BPJS Kesehatan / tidak untuk orang
		// tersebut
		// Apakah perusahaan menyediakan BPJS Ketenagakerjaan / tidak untuk orang tersebut
		$payrollCalculator->provisions->company->JKK = true;                             // Jaminan Kecelakaan Kerja
		$payrollCalculator->provisions->company->JKM = true;                             // Jaminan Kematian
		$payrollCalculator->provisions->company->JHT = true;                              // Jaminan Hari Tua
		$payrollCalculator->provisions->company->JIP = true;                              // Jaminan Pensiun
		$payrollCalculator->provisions->company->riskGrade = 1;                           // Golongan resiko ketenagakerjaan, umumnya 2
		$payrollCalculator->provisions->company->absentPenalty = 0;                       // Perhitungan nilai potongan gaji/hari sebagai penalty.
		$payrollCalculator->provisions->company->latetimePenalty = 0;                     // Perhitungan nilai keterlambatan sebagai penalty.

		$payrollCalculator->getCalculation();
		$this->payroll = $payrollCalculator;
        // $data = [$this->payroll, $attDetail];
        // $data = $this->payroll->result->deductions->JIP;
        // dd($this->payroll->result->deductions->JIP);
		return [$this->payroll, $attDetail];
        // print_r($data[0]->result->deductions->JIP);
		// return $data;
	}

	private function workDays(Employee $employee, SettingsRepository $settingsRepository, AttendanceRepository $attendanceRepository) {
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
		                   ->where('employee_id', '=', $employee->id);
		// print_r($query->toSql());
		// @formatter:on

		return $query->first();
	}

	private function workingDays(SettingsRepository $settingsRepository) {
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

	private function nationalEvents(DateTime $start, DateTime $end) {
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

			return new DateTime($event['start_date']);
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

	private function attendanceDetail(Request $request, Employee $employee, AttendanceRepository $attendanceRepository, SettingsRepository $settingsRepository,
		CalendarEventRepository $calendarEventRepository
	) {
		$self = $this;
		list($offset, $limit, $sort, $order, $search, $date, $start, $end) = $this->getDefaultRequestParam($request);
		$this->setRepository($attendanceRepository);
		$query = $this->getBaseQuery($request);

		$imonth = $start->format('n');
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, $start);
		$year = $now->format('Y');
		$month = $now->format('m');
		$imonthprev = $now->format('n');
		$start = 1;
		$days = [];
		$results = $query->with(['employee:id,name', 'reason:id,name', 'annualLeave:id,no,year,used_at'])
		                 ->where('employee_id', '=', $employee->id)
		                 ->whereDate('at', '>=', $prev)
		                 ->whereDate('at', '<=', $next)
		                 ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
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
		$events = $events->merge(CalendarEvent::whereYear('start_date', '=', $prev->format('Y'))
		                                      ->whereMonth('start_date', '=', $prev->format('n'))
		                                      ->whereDay('start_date', '=', $prev->format('j'))
		                                      ->where('recurring', '=', 0)
		                                      ->get());
		if ($next->format('n') > $prev->format('n')) {
			$events = $events->merge(CalendarEvent::whereYear('start_date', '=', $next->format('Y'))
			                                      ->whereMonth('start_date', '=', $next->format('n'))
			                                      ->whereDay('start_date', '=', $next->format('j'))
			                                      ->where('recurring', '=', 0)
			                                      ->get());
		}
		///

		for ($day = $start; $prev <= $next; $day++) {
			$isWeekend = in_array($prev->format('w'), [0, 6]);
			$event = $events->filter(function ($item) use ($prev) {
				return $item['start_date'] == $prev;
			})->first();

			$data = collect($results['data'])->filter(function ($item) use ($prev) {
				return $prev->format('Y-m-d') == (new \DateTime($item['at']))->format('Y-m-d');
			})->first();

			if (empty($data) && !($event || $isWeekend)) {
				$data = [];
				$data['start'] = null;
				$data['end'] = null;
				$data['overtime'] = null;
				$data['reason']['id'] = 5;
			}

			$att = collect($reasons)->map(function ($item, $key) use ($data) {
				if (empty($data)) return null;

				return $item == $data['reason']['id'] ? '<i class="fad fa-check"></i>' : null;
			})->toArray();

			$detail = null;
			if (!empty($data)) {
				$detail = $data['detail'] ?? null;
			}
			if ($event) $detail = $event['title'];

			$d = array_merge([
				'no'       => count($days) + 1,
				'date'     => $prev->format('l, d F Y'),
				'start'    => !empty($data) ? $data['start'] : null,
				'end'      => !empty($data) ? $data['end'] : null,
				'overtime' => !empty($data) ? $data['overtime'] : null,
				'total'    => 0,
				'remark'   => $detail,
				'event'    => $event ? true : false,
				'weekend'  => in_array($prev->format('w'), [0, 6]),
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
			$prev = $prev->add(new \DateInterval('P' . $day . 'D'));
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
