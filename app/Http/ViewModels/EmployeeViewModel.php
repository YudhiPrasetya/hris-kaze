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
use App\Models\Permit;
use App\Models\Leave;
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
use App\Http\Forms\ShowPayrollForm;

class EmployeeViewModel extends ViewModelBase {
	public $payroll;
	// public $remainingLeaveQuota;
	private ?Request $request;


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
		$this->request = null;
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

					$action = [
						'leave' => [
							'icon'    => 'fad fa-plane',
							'attr'    => [
								'class' => 'btn btn-sm btn-falcon-info',
								'href'  => route('employee.leave', ['employee' => $result['id']]),
								// 'href' => '#',
							],
							'type'    => 'a',
							'tooltip' => 'Add Leave',
						],
						'payroll' => [
							'icon'    => 'fad fa-credit-card',
							'attr'    => [
								'class' => 'btn btn-sm btn-falcon-warning',
								'href'  => route('employee.payroll', ['employee' => $result['id']]),
								'target' => '_blank'
								// 'href' => '#',
							],
							'type'    => 'a',
							'tooltip' => 'Show Payroll',
						],
					];
					$result = $self->addDefaultListActions($result);
					$actions = $result->get('actions')->merge($action);
					$result['actions'] = $actions;
					// return $self->addDefaultListActions($result);
					return $result;
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
		$fields->offsetSet('maritals_status', $this->toBool($fields->get('marital_status')));
		$fields->offsetSet('adjustment_salary', $this->toBool($fields->get('adjustment_salary')));
		// $fields->offsetSet('adjustment_salary', $fields->get('adjustment_salary'));

		// dd($fields);

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

		// $annuals = [];
		// for ($i = 0; $i < 14; $i++) {
		// 	$annuals[] = [
		// 		'no'   => sprintf('val-%02d-%s', $i + 1, date('Y')),
		// 		'year' => date('Y'),
		// 	];
		// }
		// $employee->annualLeaves()->createMany($annuals);

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

    public function forSelect(Request $request){
        return Employee::select(['id', 'name'])->get();
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
		// dd($employee);
		if(!($request->get('year') && $request->get('month'))){
			$today = new DateTime();
			$request['year'] = $today->format('Y');
			$request['month'] = $today->format('m');
		}

		$months = [
			"01" => "January",
			"02" => "February",
			"03" => "March",
			"04" => "April",
			"05" => "May",
			"06" => "June",
			"07" => "July",
			"08" => "August",
			"09" => "September",
			"10" => "October",
			"11" => "November",
			"12" => "December",
		];

		$yearStart = $request['year'];
		$monthStart = $request['month'];
		$cutOffDateStart = $settingsRepository->findOneBySectionAndKey('attendance', 'cutoff');
		$startDate = $yearStart . "-" . $monthStart . "-" . (string)$cutOffDateStart->value + 1;
		$start = new DateTime($startDate);
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, $start);

		// $att = $this->workDays($employee, $settingsRepository, $attendanceRepository, $start);
		// dump($att);
		// $workingDays = count($this->workingDays($settingsRepository, $request));
		// dump($workingDays);
		$attDetail = $this->attendanceDetail($request, $employee, $attendanceRepository, $settingsRepository, $calendarEventRepository);
		// dump($attDetail);
		$begin = clone($prev);
		$end = clone($next);
		$events = $this->nationalEvents($begin, $end);
		$totalHolidays = 0;
		$totalWorksDayMonth = 0;
		while($begin <= $end){
			$isWeekend = in_array($begin->format('w'), [0, 6]);
			$event = collect($events)->filter(function ($item) use ($begin) {
				// dump($item);
				return $item == $begin;
			})->first();
			if($isWeekend || $event != null) $totalHolidays++;
			if( !$isWeekend && $event == null) $totalWorksDayMonth++;
			$begin->modify('+1 day');
		}
		// dump($totalWorksDayMonth);
		$overtimeOutbound = [];
		$overtimesOutbound = 0;

		$overtimeInbound = [];
		$overtimesInbound = 0;

		$totalPresences = 0;
		$totalAbsents = 0;
		$totalWorkDays = 0;
		$totalSick = 0;
		$totalLeaves = 0;
		$totalPermit = 0;
		$totalBusinessTrip = 0;
		$totalPermits = 0;
		$totalCutAttPremium = 0;
		$totalCutAttSalary = 0;
		// $totalHolidays = 0;
		foreach ($attDetail as $detail) {
            // dump($detail);
			$totalPresences += $detail['present'] != null ? 1 : 0;

			$totalPresences += $detail['business_trip'] != null ? 1 : 0;

			$totalWorkDays += ($detail['present'] != null ? 1 : 0) + ($detail['sick'] != null ? 1 : 0) + ($detail['business_trip'] != null ? 1 : 0) + ($detail['permit'] != null ? 1 : 0) + ($detail['annual_leave'] != null ? 1 : 0);

			$totalSick += $detail['sick'] != null ? 1 : 0;

			$totalPermit += $detail['permit'] != null ? 1 : 0;

			$totalBusinessTrip += $detail['business_trip'] != null ? 1 : 0;

			$totalCutAttPremium += $detail['cut_att_premium'] != null ? (int)$detail['cut_att_premium'] : 0;

			$totalCutAttSalary += $detail['cut_att_salary'] != null ? (int)$detail['cut_att_salary'] : 0;

			$totalLeaves += $detail['annual_leave'] != null ? 1 : 0;

			$totalAbsents += $detail['absent'] != null ? 1 : 0;

			if ($detail['total'] != 0) {
				if($detail['overtime_confirmed'] == 1){
                    if(Carbon::parse($detail['date'])->isWeekend()){
                        array_push($overtimeOutbound, [
                            'at'       => DateTime::createFromFormat('l, d F Y', $detail['date'])->format('Y-m-d'),
                            'start'    => $detail['start'],
                            'end'      => $detail['end'],
                            'overtime_outbound' => $detail['overtime'],
                        ]);
                        // $overtimeOutbound[] = [
                        //     'at'       => DateTime::createFromFormat('l, d F Y', $detail['date'])->format('Y-m-d'),
                        //     'start'    => $detail['start'],
                        //     'end'      => $detail['end'],
                        //     'overtime_outbound' => $detail['overtime'],
                        // ];
                        ++$overtimesOutbound;
                    }else{
                        array_push($overtimeInbound, [
                            'at'       => DateTime::createFromFormat('l, d F Y', $detail['date'])->format('Y-m-d'),
                            'start'    => $detail['start'],
                            'end'      => $detail['end'],
                            'overtime_inbound' => $detail['overtime'],
                        ]);
						// $overtimeInbound[] = [
						// 	'at'       => DateTime::createFromFormat('l, d F Y', $detail['date'])->format('Y-m-d'),
						// 	'start'    => $detail['start'],
						// 	'end'      => $detail['end'],
						// 	'overtime_inbound' => $detail['overtime'],
					    // ];
                        ++$overtimesInbound;
                    }
					// if (Carbon::parse($detail['date'])->isWeekend()) ++$overtimes;

				}
			}

        }
        // dump($overtimeInbound);

		$totalPermits = $totalSick + $totalPermit + $totalBusinessTrip;

		// $totalOvertimeOutbound = $this->totalHours($overtimeOutbound);
		$totalOvertimeOutbound = $this->totalOvertimeOutboundHours($overtimeOutbound);

		// $totalOvertimeInbound = $this->totalHours($overtimeInbound);
		$totalOvertimeInbound = $this->totalOvertimeInboundHours($overtimeInbound);

		// cari izin potong gaji
		// $permitCutSalary = Permit::where('id_employee', '=', $employee->id)->get()->count();
		// dump($totalOvertime['hours']);
		$payrollCalculator = new PayrollCalculator();
		$payrollCalculator->method = PayrollCalculator::GROSS_CALCULATION;
		$payrollCalculator->taxNumber = PayrollCalculator::PPH21;
		$payrollCalculator->company->period = $months[$monthStart] . " " . $yearStart . ' (' . $prev->format('d F Y') . ' - ' . $next->format('d F Y') . ')';
		$payrollCalculator->company->month = $months[$monthStart] . " " . $yearStart;
		$payrollCalculator->employee->earnings->base = $employee->basic_salary ?? 0;
		$payrollCalculator->employee->earnings->functionalAllowance = $employee->functional_allowance ?? 0;
		$payrollCalculator->employee->allowances->transportAllowance = $employee->transport_allowance ?? 0;
		$payrollCalculator->employee->allowances->mealAllowances = $employee->meal_allowances ?? 0;
		$payrollCalculator->employee->earnings->eidEarnings = $employee->eid_allowance ?? 0;
		$payrollCalculator->employee->allowances->otherAllowance = $employee->other_allowance ?? 0;
		$payrollCalculator->employee->permanentStatus = $employee->permanent_status;
		$payrollCalculator->employee->permits->cuttAttPremium = $totalCutAttPremium > 0;

		$payrollCalculator->remainingLeaveQuota = $this->countRemainLeaveQuota($employee);
		// $payrollCalculator->employee->presences->workDays = $totalPresence;
		$payrollCalculator->provisions->company->numOfWorkingDays = $totalWorksDayMonth;                   // jumlah hari masuk kerja
		$payrollCalculator->employee->presences->workDays = $totalWorkDays;                    // jumlah hari masuk kerja
		$payrollCalculator->employee->presences->sickDays = $totalSick;
		$payrollCalculator->employee->presences->leaveDays = $totalLeaves;
		$payrollCalculator->employee->presences->travelDays = $totalBusinessTrip;
		$payrollCalculator->employee->presences->permits = $totalPermit;
		$payrollCalculator->employee->presences->holidayDays = $totalHolidays;


		// dump('totalAbsent: '. $totalAbsent);
		// dump($payrollCalculator->employee->earnings->base / $payrollCalculator->employee->presences->workDays);
		// dump($totalCutAttSalary + $totalAbsent);

		// Gaji pokok adjustment = gaji pokok / workDays x (workDays + cuti - izin)

		// if($employee->adjustment_salary == 1){
        if($totalCutAttSalary > 0){

            // $totalAbsentAndLeave = $totalAbsent + $totalLeave;

            // dump('baseSalary: ' . $payrollCalculator->employee->earnings->base);
            // dump('workDays: ' . $totalWorkDays);
            // dump('permits: '. $totalPermits);
            // dump('sick: '. $totalSick);
            // dump('absent: ' . $totalAbsent);
            // dump('leave: ' . $totalLeave);
            $totalWorkDays = $totalPresences + $totalBusinessTrip + $totalLeaves + $totalSick + $totalPermit;


            // $baseSalaryAdjustment = ceil(($payrollCalculator->employee->earnings->base / $totalWorkDays) * ($totalWorkDays + $totalPermits + $totalSick - $totalAbsentAndLeave));
            // $baseSalaryAdjustment = ceil(($payrollCalculator->employee->earnings->base / $totalWorkDays) * ($totalWorkDays + $totalLeaves -($totalPermits + $totalAbsents)));
            $baseSalaryAdjustment = ceil(($payrollCalculator->employee->earnings->base / $totalWorkDays) * ($totalWorkDays-($totalPermits + $totalAbsents)));

			// dump('baseSalaryAdjustment: ' . $baseSalaryAdjustment);

			$payrollCalculator->employee->earnings->baseSalaryAdjustment = $baseSalaryAdjustment;
		}else{
			$payrollCalculator->employee->earnings->baseSalaryAdjustment = $payrollCalculator->employee->earnings->base;
		}

        // $payrollCalculator->employee->presences->workDays = $totalWorkDays;                    // jumlah hari masuk kerja
        // dump($totalWorkDays);
		// $payrollCalculator->employee->presences->overtimeHours = $totalOvertime['hours'] ?? 0;           // perhitungan jumlah lembur dalam jam
		$payrollCalculator->employee->presences->overtimeOutboundHours = $totalOvertimeOutbound['hours'] ?? 0;           // perhitungan jumlah lembur outbound dalam jam
		$payrollCalculator->employee->presences->overtimeInboundHours = $totalOvertimeInbound['hours'] ?? 0;           // perhitungan jumlah lembur inbound dalam jam

		// dd($totalOvertime['hours']);

		// dd('workingDays: '. $workingDays . ' totalWorkDays: ' . $totalWorkDays);

		$payrollCalculator->employee->presences->absentDays = $totalAbsents; // perhitungan jumlah alpha
		$payrollCalculator->employee->presences->rate = $employee->attendance_premium ?? 0;

		// $payrollCalculator->employee->permanentStatus = $employee->permanent_status;
		// $payrollCalculator->employee->maritalStatus = $employee->marital_status;
		// $payrollCalculator->employee->hasNPWP = $employee->has_npwp;
		$payrollCalculator->employee->numOfDependentsFamily = $employee->num_of_dependents_family;
		// $payrollCalculator->employee->earnings->base = $employee->basic_salary;
		// $payrollCalculator->employee->earnings->fixedAllowance = (int)($employee->functional_allowance + $employee->transport_allowance + $employee->meal_allowances + $employee->other_allowance);


		// $payrollCalculator->employee->presences->workDays = $att->present ?? 0;                    // jumlah hari masuk kerja
		// $payrollCalculator->employee->presences->overtimeDays = $overtimes ?? 0;           // perhitungan jumlah lembur dalam satuan jam
		// $payrollCalculator->employee->presences->overtimeDays = count($totalOvertime) ?? 0;           // perhitungan jumlah lembur dalam satuan jam
		$payrollCalculator->employee->presences->overtimeDays = count($totalOvertimeOutbound + $totalOvertimeInbound) ?? 0;           // perhitungan jumlah lembur dalam satuan jam
		// $payrollCalculator->employee->presences->overtime = $totalovertime['hours'] ?? 0;           // perhitungan jumlah lembur dalam satuan jam
		// $payrollCalculator->employee->presences->overtimeHours = $totalovertime['hours'] ?? 0;
		// $payrollCalculator->employee->presences->overtimeMinutes = $totalovertime['minutes'] ?? 0;
		// $payrollCalculator->employee->presences->latetime = 0;                                      // perhitungan jumlah keterlambatan dalam satuan jam
		// $payrollCalculator->employee->presences->travelDays = $att->business_trip ?? 0;             // perhitungan jumlah hari kepergian dinas
		// $payrollCalculator->employee->presences->indisposedDays = $att->sick ?? 0;                  // perhitungan jumlah hari sakit yang telah memiliki surat dokter
		// $payrollCalculator->employee->presences->permits = $att->permit ?? 0;                  // perhitungan jumlah hari sakit yang telah memiliki surat dokter
		// $payrollCalculator->employee->presences->absentDays =  (count($workingDays) - ($att->present+ $att->sick + $att->business_trip + $att->permit + $att->annual_leave ?? 0)) ?? 0;                    // perhitungan jumlah hari alpha
		// $payrollCalculator->employee->presences->splitShifts = 0;                                   // perhitungan jumlah split shift

		// [$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, $start);

		// $permitCutAttPremium = Permit::where('id_employee',$employee->id)->where('start', '>=', $prev)->where('end', '<=', $next)->where('cut_att_premium',1)->count();
		// $permitCuttSalary = Permit::where('id_employee',$employee->id)->where('start', '>=', $prev)->where('end', '<=', $next)->where('cut_att_salary',1)->count();
		// $leaveCutAttPremium = Leave::where('id_employee',$employee->id)->where('start', '>=', $prev)->where('end', '<=', $next)->where('cut_att_premium',1)->count();

		// $daylySalary = $payrollCalculator->employee->earnings->base / count($workingDays);

		// // Hitung total potongan gaji akibat cuti dan izin yang memotong gaji
		// $permittCuttSalary = $permitCuttSalary * $daylySalary;

		// // $payrollCalculator->employee->presences->rate = $employee->attendance_premium ?? 0;
		// $payrollCalculator->employee->presences->rate = ($permitCutAttPremium > 0 || $leaveCutAttPremium > 0) ? 0 : $employee->attendance_premium;
		// $payrollCalculator->employee->presences->overtimeRate = $employee->overtime ?? 0;
		// $payrollCalculator->employee->employeeCuttSalary = $permittCuttSalary; // Set potongan gaji akibat cuti dan izin yang memotong gaji

		// //$payrollCalculator->employee->presences->absentDays = count($workingDays) - $totalWorkDays; // perhitungan jumlah split shift
		// // Set data tunjangan karyawan di luar tunjangan BPJS Kesehatan dan Ketenagakerjaan
		// $payrollCalculator->employee->allowances->offsetSet('meal', $employee->meal_allowances ?? 0);
		// $payrollCalculator->employee->allowances->offsetSet('transport', $employee->transport_allowance ?? 0);
		// $payrollCalculator->employee->allowances->offsetSet('functional', $employee->functional_allowance ?? 0);
		// $payrollCalculator->employee->allowances->offsetSet('other', $employee->other_allowance ?? 0);

		// $payrollCalculator->provisions->state->overtimeRegulationCalculation = false;     // Jika false maka akan dihitung sesuai kebijakan perusahaan
		// $payrollCalculator->provisions->state->provinceMinimumWage = 6000000;             // Ketentuan UMP sesuai propinsi lokasi perusahaan

		// // Set data ketentuan perusahaan
		// $payrollCalculator->provisions->company->numOfWorkingDays = count($workingDays);  // Jumlah hari kerja dalam satu bulan
		// $payrollCalculator->provisions->company->numOfWorkingHours = 8;                   // Jumlah jam kerja dalam satu hari
		// $payrollCalculator->provisions->company->overtimeRate = $employee->overtime ?? 0;      // Rate lembur perjam
		// $payrollCalculator->provisions->company->calculateOvertime = false;               // Apakah perusahaan menghitung lembur
		// $payrollCalculator->provisions->company->calculateSplitShifts = false;            // Apakah perusahan menghitung split shifts
		// $payrollCalculator->provisions->company->splitShiftsRate = 25000;                 // Rate Split Shift perusahaan
		// $payrollCalculator->provisions->company->calculateBPJSKesehatan = true;           // Apakah perusahaan menyediakan BPJS Kesehatan / tidak untuk orang tersebut

		// // Apakah perusahaan menyediakan BPJS Ketenagakerjaan / tidak untuk orang tersebut
		// $payrollCalculator->provisions->company->JKK = true;                             // Jaminan Kecelakaan Kerja
		// $payrollCalculator->provisions->company->JKM = true;                             // Jaminan Kematian
		// $payrollCalculator->provisions->company->JHT = true;                              // Jaminan Hari Tua
		// $payrollCalculator->provisions->company->JIP = true;                              // Jaminan Pensiun
		// $payrollCalculator->provisions->company->riskGrade = 1;                           // Golongan resiko ketenagakerjaan, umumnya 2
		// $payrollCalculator->provisions->company->absentPenalty = 0;                       // Perhitungan nilai potongan gaji/hari sebagai penalty.
		// $payrollCalculator->provisions->company->latetimePenalty = 0;                     // Perhitungan nilai keterlambatan sebagai penalty.

		// $payrollCalculator->employee->presences->overtimeHours = OvertimeViewModel::getOvertimeTotalHours($settingsRepository, $request);
		$payrollCalculator->getCalculation();
		$this->payroll = $payrollCalculator;

		return [$this->payroll, $attDetail];
	}
	public function countRemainLeaveQuota($emp): string|int{
		$years = (new DateTime())->diff($emp->effective_since)->y;
		if($years >=1){
			$year = date('Y');
			$count = Attendance::where('employee_id', $emp->id)
									->where('attendance_reason_id', '=', 6)
									->whereYear('at', '=', (int)$year)->get()->count();

			return 12-$count;
		}
		return "Haven't received leave quota yet (belum mendapat jatah kuota cuti).";
		// return $remainingLeaveQuota;
		// return ['remainingLeaveQuota' => $remainingLeaveQuota];
	}

	private function workDays(Employee $employee, SettingsRepository $settingsRepository, AttendanceRepository $attendanceRepository, DateTime $start) {
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, $start);

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

	private function workingDays(SettingsRepository $settingsRepository, Request $request) {
		$year = $request['year'];
		// $monthStart = (int)$request['month']-1;
		// $monthEnd = (int)$request['month'];

		$startDate = $year . "-" . $request['month'] . "-" . "26";
		$start = new DateTime($startDate);

		// [$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, new \DateTime());
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, $start);

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
				$d = new DateTime($event['start_date']);
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
		list($offset, $limit, $sort, $order, $search, $date, $startDate, $end) = $this->getDefaultRequestParam($request);
		$this->setRepository($attendanceRepository);
		$query = $this->getBaseQuery($request);

		$imonth = $startDate->format('n');
		// dump($imonth);
		// dump('startDate: ' . $startDate->format('Y-m-d'));
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, $startDate);
		// dump('prev: ' . $prev->format('Y-m-d') . ' next: ' . $next->format('Y-m-d'));

		$year = $now->format('Y');
		// $month = $now->format('m');
		$month = $next->format('m');

		// $imonthprev = $now->format('n');
		// $imonthprev = $now->format('n');
		$imonthprev = $prev->format('n');

		$start = 1;
		$days = [];
		$results = $query->with(['employee:id,name', 'reason:id,name'])
		                 ->where('employee_id', '=', $employee->id)
		                 ->whereDate('at', '>=', $prev)
		                 ->whereDate('at', '<=', $next)
		                 ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();
		$reasons = ['present' => 1, 'sick' => 2, 'business_trip' => 3, 'permit' => 4, 'absent' => 5, 'annual_leave' => 6];

		// foreach($results['data'] as $data){
		// 	dump((new DateTime($data['at']))->format('Y-m-d'));
		// 	// dump($data);
		// 	// dump(count($data));
		// }
		// dump(count($results['data']));

		// $reasons = ['1' => 'present', '2' => 'sick', '3' => 'business_trip', '4' => 'permit', '5' => 'absent', '6' => 'annual_leave'];

		///
		// $events = CalendarEvent::whereMonth('start_date', '=', $imonthprev)
		//                        ->where('recurring', '=', 1)
		//                        ->get()
		//                        ->map(function ($item) use ($year) {
		// 	                       $item['start_date'] = new DateTime($year . '-' . (new DateTime($item['start_date']))->format('m-d'));

		// 	                       return $item;
		//                        });

		// $events = CalendarEvent::whereDate('start_date', '>=', $prev)
		// 						->whereDate('start_date', '<=', $next)
		//                        	->where('recurring', '=', 1)
		// 						->orWhere('recurring', '=', 0)
		//                        	->get()
		//                        	->map(function ($item) use ($year) {
		// 	                       $item['start_date'] = new DateTime($year . '-' . (new DateTime($item['start_date']))->format('m-d'));

		// 						   dump($item['start_date']);

		// 	                       return $item;
		//                        	});

		// dd($events);

		// $events = $events->merge(CalendarEvent::whereMonth('start_date', '=', $imonth)
		//                                       ->where('recurring', '=', 1)
		//                                       ->get()
		//                                       ->map(function ($item) use ($year) {
		// 	                                      $item['start_date'] = new DateTime($year . '-' . (new DateTime($item['start_date']))->format('m-d'));

		// 	                                      return $item;
		//                                       }));


		// $events = $events->merge(CalendarEvent::whereYear('start_date', '=', $prev->format('Y'))
		//                                       ->whereMonth('start_date', '=', $prev->format('n'))
		//                                       ->whereDay('start_date', '=', $prev->format('j'))
		//                                       ->where('recurring', '=', 0)
		//                                       ->get());

		// if ($next->format('n') > $prev->format('n')) {
		// 	$events = $events->merge(CalendarEvent::whereYear('start_date', '=', $next->format('Y'))
		// 	                                      ->whereMonth('start_date', '=', $next->format('n'))
		// 	                                      ->whereDay('start_date', '=', $next->format('j'))
		// 	                                      ->where('recurring', '=', 0)
		// 	                                      ->get());
		// }
		///
		// for ($day = $start; $prev <= $next; $day++) {
		$events = $this->nationalEvents(clone($prev), clone($next));
		// $eventsCollection = collect($events);
		// dd($eventsCollection);
        // dump($results['data']);
		while ($prev <= $next) {
			$isWeekend = in_array($prev->format('w'), [0, 6]);
			$event = collect($events)->filter(function ($item) use ($prev) {
				// dump($item);
				return $item == $prev;
			})->first();

			$data = collect($results['data'])->filter(function ($item) use ($prev) {
				// dump($item);
				// dump($prev->format('Y-m-d') == (new DateTime($item['at']))->format('Y-m-d'));
				return $prev->format('Y-m-d') == (new DateTime($item['at']))->format('Y-m-d');
			})->last();
            // dump($data);
			if (empty($data) && !($event || $isWeekend)) {
			// if (empty($data)) {
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
			// dd($att);

			$detail = null;
			if (!empty($data)) {
				$detail = $data['detail'] ?? null;
			}
			// if ($event) $detail = $event['title'];
			// dump($data['overtime_confirmed']);
			$d = array_merge([
				'no'       => count($days) + 1,
				'date'     => $prev->format('l, d F Y'),
				'start'    => !empty($data) ? $data['start'] : null,
				'end'      => !empty($data) ? $data['end'] : null,
				'overtime' => !empty($data) ? $data['overtime'] : null,
				'overtime_confirmed' => !empty($data) ? ($data['overtime_confirmed'] ?? false) : null,
				'cut_att_premium' => !empty($data) ? ($data['cut_att_premium'] ?? false) : null,

				// 'cut_att_premium' => !empty($data) ? $data['cut_att_premium'] : null,

				'cut_att_salary' => !empty($data) ? ($data['cut_att_salary'] ?? false) : null,
				// 'cut_att_salary' => !empty($data) ? $data['cut_att_salary'] : null,

				'total'    => 0,
				'remark'   => $detail,
				// 'event'    => $event ? true : false,
				'weekend'  => in_array($prev->format('w'), [0, 6]),
			], $att);

			if (!empty($d['start']) || !empty($d['end']) || !empty($d['overtime'])) {
				$day1hours = new DateTime(sprintf("%s %s", (new DateTime($data['at']))->format('Y-m-d'), $d['start']));

				if (empty($d['end'])) {
					$d['total'] = "00:00";
					$day2hours = clone($day1hours);
				}
				else {
					$day2hours = new DateTime(sprintf("%s %s", (new DateTime($data['at']))->format('Y-m-d'), $d['end']));
					$d['total'] = $day2hours->diff($day1hours)->format('%H:%I');
				}

				if (!empty($d['overtime'])) {
					$overtime = (new DateTime($d['overtime']))->diff($day2hours)->format('%H:%I');
					$parts = explode(':', $d['total']);
					$overtime = (new DateTime($overtime))->add(new \DateInterval('PT' . intval($parts[0]) . 'H' . intval($parts[1]) . 'M'));
					$d['total'] = $overtime->format('H:i');
				}

				if ($d['weekend'] && empty($d['remark'])) $d['remark'] = "Overtime";
			}

			$days[] = $d;

			// dump($prev->format('Y-m-d'));
			// $prev = (new DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateStart))));
			// $prev = $prev->add(new \DateInterval('P' . $day . 'D'));
			$prev = $prev->add(new \DateInterval('P1D'));
		}

		return $days;
	}

	private function totalHours(array $hourMin) {
		$hours = 0;
		$mins = 0;
		// $totalHours = 0;

		foreach ($hourMin as $val) {
			// if (!empty($val['overtime'])) {
			if (!empty($val['start']) && !empty($val['end'])) {
				// $hours1 = new \DateTime(sprintf("%s %s", (new \DateTime($val['at']))->format('Y-m-d'), $val['end']));
				$hours1 = new DateTime(sprintf("%s %s", (new DateTime($val['at']))->format('Y-m-d'), $val['start']));

				// $hours2 = new \DateTime(sprintf("%s %s", (new \DateTime($val['at']))->format('Y-m-d'), $val['overtime']));
				$hours2 = new DateTime(sprintf("%s %s", (new DateTime($val['at']))->format('Y-m-d'), $val['end']));
				$overtime = $hours1->diff($hours2)->format('%H:%I');

				$explodeHoursMins = explode(':', $overtime);

				$hours += (int)$explodeHoursMins[0] > 8 ? 8 : (int)$explodeHoursMins[0];
				// $hours = $hours > 8 ? 8 : $hours;
				// $totalHours += $hours;
				// $mins += (int)$explodeHoursMins[1];
			}
		}

		$minToHours = date('H:i', mktime(0, $mins)); //Calculate Hours From Minutes
		$explodeMinToHours = explode(':', $minToHours);
		$hours += (int)$explodeMinToHours[0];
		$finalMinutes = (int)$explodeMinToHours[1];

		return ['hours' => (int)$hours, 'minutes' => (int)$finalMinutes];
	}

	private function totalOvertimeInboundHours(array $hourMin) {
		$hours = 0;
		$mins = 0;
		// $totalHours = 0;

		foreach ($hourMin as $val) {
			// if (!empty($val['overtime'])) {
			if (!empty($val['start']) && !empty($val['end'])) {
				// $hours1 = new \DateTime(sprintf("%s %s", (new \DateTime($val['at']))->format('Y-m-d'), $val['end']));
				$hours1 = new DateTime(sprintf("%s %s", (new DateTime($val['at']))->format('Y-m-d'), $val['end']));

				// $hours2 = new \DateTime(sprintf("%s %s", (new \DateTime($val['at']))->format('Y-m-d'), $val['overtime']));
				$hours2 = new DateTime(sprintf("%s %s", (new DateTime($val['at']))->format('Y-m-d'), $val['overtime_inbound']));
				$overtime = $hours1->diff($hours2)->format('%H:%I');

				$explodeHoursMins = explode(':', $overtime);

				$hours += (int)$explodeHoursMins[0] > 8 ? 8 : (int)$explodeHoursMins[0];
				// $hours = $hours > 8 ? 8 : $hours;
				// $totalHours += $hours;
				// $mins += (int)$explodeHoursMins[1];
			}
		}

		$minToHours = date('H:i', mktime(0, $mins)); //Calculate Hours From Minutes
		$explodeMinToHours = explode(':', $minToHours);
		$hours += (int)$explodeMinToHours[0];
		$finalMinutes = (int)$explodeMinToHours[1];

		return ['hours' => (int)$hours, 'minutes' => (int)$finalMinutes];
	}

	private function totalOvertimeOutboundHours(array $hourMin) {
		$hours = 0;
		$mins = 0;
		// $totalHours = 0;

		foreach ($hourMin as $val) {
			// if (!empty($val['overtime'])) {
			if (!empty($val['start']) && !empty($val['end'])) {
				// $hours1 = new \DateTime(sprintf("%s %s", (new \DateTime($val['at']))->format('Y-m-d'), $val['end']));
				$hours1 = new DateTime(sprintf("%s %s", (new DateTime($val['at']))->format('Y-m-d'), $val['end']));

				// $hours2 = new \DateTime(sprintf("%s %s", (new \DateTime($val['at']))->format('Y-m-d'), $val['overtime']));
				$hours2 = new DateTime(sprintf("%s %s", (new DateTime($val['at']))->format('Y-m-d'), $val['overtime_outbound']));
				$overtime = $hours1->diff($hours2)->format('%H:%I');

				$explodeHoursMins = explode(':', $overtime);

				$hours += (int)$explodeHoursMins[0] > 8 ? 8 : (int)$explodeHoursMins[0];
				// $hours = $hours > 8 ? 8 : $hours;
				// $totalHours += $hours;
				// $mins += (int)$explodeHoursMins[1];
			}
		}

		$minToHours = date('H:i', mktime(0, $mins)); //Calculate Hours From Minutes
		$explodeMinToHours = explode(':', $minToHours);
		$hours += (int)$explodeMinToHours[0];
		$finalMinutes = (int)$explodeMinToHours[1];

		return ['hours' => (int)$hours, 'minutes' => (int)$finalMinutes];
	}

   public function setRequest(Request $request)
   {
      $this->request = $request;

      return $this;
   }

   public function request()
   {
      return $this->request;
   }

   public function createShowPayrollForm(string $method, string $route, array $options = []): ViewModelBase
   {
	  $this->form = $this->formBuilder->create(ShowPayrollForm::class, $options);
	  $this->form->setMethod($method);
	  $this->form->setUrl(route($route, ['employee' => $this->model->id]));

	  return $this;
   }
}
