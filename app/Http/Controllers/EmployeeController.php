<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeFormRequest;
use App\Http\ViewModels\EmployeeViewModel;
use App\Http\ViewModels\ViewModel;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Http\ViewModels\ViewModelBase;
use App\Managers\Form\FormBuilder;
use App\Models\CalendarEvent;
use App\Models\Employee;
use App\Repositories\Eloquent\AttendanceRepository;
use App\Repositories\Eloquent\CalendarEventRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\SettingsRepository;
use Faker\Factory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

use App\Http\ViewModels\LeaveViewModel;
use App\Repositories\Eloquent\LeaveRepository;
use App\Models\Leave;
use App\Http\ViewModels\AttendanceViewModel;
use App\Libraries\Payroll\PayrollCalculator;
use PDF;

class EmployeeController extends Controller {
	private EmployeeViewModel $viewModel;
	private SettingsRepository $settingsRepository;
	private AttendanceRepository $attendanceRepository;
	private CalendarEventRepository $calendarEventRepository;

	private LeaveViewModel $leaveViewModel;

	private AttendanceViewModel $attendanceViewModel;
	private LeaveRepository $leaveRepository;

   	
	/**
	 * EmployeeController constructor.
	 *
	 * @param \App\Repositories\Eloquent\EmployeeRepository $repository
	 * @param \App\Managers\Form\FormBuilder $builder
	 *
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function __construct(EmployeeRepository $repository, SettingsRepository $settingsRepository, 
								AttendanceRepository $attendanceRepository, CalendarEventRepository $calendarEventRepository, 
								LeaveRepository $leaveRepository, FormBuilder $builder) 
	{
		$this->viewModel = new EmployeeViewModel($repository, $builder);
		$this->settingsRepository = $settingsRepository;
		$this->attendanceRepository = $attendanceRepository;
		$this->calendarEventRepository = $calendarEventRepository;
		$this->leaveRepository = $leaveRepository;
		$this->leaveViewModel = new LeaveViewModel($leaveRepository, $builder);
		$this->attendanceViewModel = new AttendanceViewModel($attendanceRepository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return EmployeeViewModel|ViewModel
	 */
	public function index(): HttpViewModel|EmployeeViewModel {
		return $this->viewModel->view('pages.employee.list');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \App\Http\Requests\EmployeeFormRequest $request
	 *
	 * @return EmployeeViewModel|ViewModel|Application|RedirectResponse|Redirector
	 * @throws \Exception
	 */
	public function store(EmployeeFormRequest $request): HttpViewModel|EmployeeViewModel|Redirector|RedirectResponse|Application {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('employee.show', ['employee' => $model->id]));
		}

		return $this->create();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return EmployeeViewModel|ViewModel
	 * @throws \Exception
	 */
	public function create(): EmployeeViewModel|HttpViewModel {
		$employee = new Employee();

		// if (is_devel()) {
		// 	$locales = get_locales();
		// 	$rand = $locales[array_search('en_US', $locales)];
		// 	$gender = "male";
		// 	$faker = Factory::create($rand);
		// 	$date = new \DateTime();
		// 	$employee->name = $faker->name($gender);
		// 	$employee->nik = $faker->numberBetween(10000000000, 99999999999);
		// 	$employee->position_id = 1;
		// 	$employee->gender_id = $gender === "male" ? 1 : 0;
		// 	$employee->effective_since = $date->format('Y-m-d');
		// 	$date->sub(new \DateInterval('P27Y'));
		// 	$employee->birth_date = $date->format('Y-m-d');
		// 	$employee->basic_salary = $faker->numberBetween(3000000, 10000000);
		// 	$employee->meal_allowances = $employee->basic_salary * 0.1;
		// 	$employee->transport_allowance = $employee->basic_salary * 0.2;
		// 	$employee->functional_allowance = $employee->basic_salary * 0.5;
		// 	$employee->postal_code = $faker->postcode;
		// 	$employee->state_id = 1620;
		// 	$employee->city_id = 143160;
		// 	$employee->district_id = 1959;
		// 	$employee->village_id = 25589;
		// 	$employee->street = $faker->streetAddress;
		// }

		// $self = $this;
		// collect(['ip', 'port'])->each(function($item) use($self) {
		// 	$result = $self->settingsRepository->findOneBySectionAndKey('attendance', $item);
		// 	$self->viewModel->addData($item, $result);
		// });

		return $this->viewModel->createForm('POST', 'employee.store', $employee)
		                       ->view('pages.employee.form');
	}

	public function getpayroll(Request $request, Employee $employee) {
		$this->viewModel->setModel($employee);
		return $this->viewModel->payrollCalc($request, $this->settingsRepository, $this->attendanceRepository, $this->calendarEventRepository);
	}


	public function showPayroll(Request $request){
		// dd($request);
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

		$month = $request->month;
		$year = $request->year;

		$employee = Employee::find($request->employee);
		$this->viewModel->setModel($employee);

		$employeePayroll = $this->viewModel->payrollCalc($request, $this->settingsRepository, $this->attendanceRepository, $this->calendarEventRepository);
		$payrollCalc = new PayrollCalculator();
		$payrollCalc = $employeePayroll[0];

		// // dd($moneyFormat($employee->basic_salary, $employee->currencyCode()));

		// // $employeePayroll = $this->viewModel->payrollCalc($request, $this->settingsRepository, $this->attendanceRepository, $this->calendarEventRepository);
		$totalEarnings = $payrollCalc->result->earnings->baseTotal + $payrollCalc->employee->presences->rate + $payrollCalc->result->earnings->overtime;
		$takeHomePay = $payrollCalc->result->takeHomePay + $payrollCalc->employee->presences->rate + $payrollCalc->result->earnings->overtime;
		$data = [
			'periode' => $months[$month] . "/" . $year,
			'nik' => $employee->nik, 
			'name' => $employee->name,
			'jobTitle' => $employee->jobTitle()->first()->name,
			'position' => $employee->position()->first()->name,
			'basicSalary' => $employee->basic_salary,
			'functionalAllowance' => $employee->functional_allowance,
			'transportAllowance' => $employee->transport_allowance,
			'mealAllowance' => $employee->meal_allowances,
			'otherAllowance' => $employee->other_allowance,
			'overtimeDays' => $payrollCalc->employee->presences->overtimeDays,
			'overtimeEarnings' => $payrollCalc->result->earnings->overtime,
			// 'attendancePremium' => $payrollCalc->result->earnings->attendance_premium,
			'attendancePremium' => $payrollCalc->employee->presences->rate,
			'totalDependents' => $payrollCalc->employee->numOfDependentsFamily,
			'BPJSKes' => $payrollCalc->result->deductions->BPJSKesehatan,
			'JHT' => $payrollCalc->result->deductions->JHT,
			'JIP' => $payrollCalc->result->deductions->JIP,
			'PPH21' => $payrollCalc->result->deductions->pph21Tax,
			'taxableRate' => $payrollCalc->result->taxable->rate,
			'presences' => $payrollCalc->employee->presences->workDays,
			'workingDays' => $payrollCalc->provisions->company->numOfWorkingDays,
			'presencesDeduction' => $payrollCalc->result->deductions->presence,
			'present' => $payrollCalc->employee->presences->workDays,
			'sick' => $payrollCalc->employee->presences->indisposedDays,
			'businessTrip' => $payrollCalc->employee->presences->travelDays,
			'permit' => $payrollCalc->employee->presences->permits,
			'absent' => $payrollCalc->employee->presences->absentDays,
			'nett' => $payrollCalc->result->earnings->annually->nett,
			'statusPTKP' => $payrollCalc->result->taxable->ptkp->status,
			'amountPTKP' => $payrollCalc->result->taxable->ptkp->amount,
			'PKP' => $payrollCalc->result->taxable->pkp,
			'PPH21PerBulan' => $payrollCalc->result->taxable->liability->monthly,
			'PPH21PerTahun' => $payrollCalc->result->taxable->liability->annual,
			// 'totalEarnings' => $payrollCalc->result->earnings->baseTotal,
			'totalEarnings' => $totalEarnings,
			'totalDeductions' => $payrollCalc->result->deductions->getSum() - $payrollCalc->result->deductions->positionTax,
			// 'takeHomePay' => $payrollCalc->result->takeHomePay
			'takeHomePay' => $takeHomePay
		];

		// dd($data);

		// $this->viewModel->payrollCalc($request, $this->settingsRepository, $this->attendanceRepository, $this->calendarEventRepository);
		// $payrollCalc = new PayrollCalculator();
		// $payrollCalc = $employeePayroll[0];
		// $data = [
		// 	'model' => $employeeModel,

		// ];

		// dump($payrollCalc->employee, $payrollCalc->result->takeHomePay);
		// return $this->viewModel->payrollCalc($request, $this->settingsRepository, $this->attendanceRepository, $this->calendarEventRepository);

		set_time_limit(300);
		$pdf = PDF::loadView('pages.employee.payroll', $data);
		// $this->viewModel->payrollCalc($request, $this->settingsRepository, $this->attendanceRepository, $this->calendarEventRepository);

		return $pdf->download('Payroll-' . $data['name'] . '-' . $data['periode'] . '.pdf');

		// 												 ->with('alert', 'success');

		// return $this->viewModel->payrollCalc($request, $this->settingsRepository, $this->attendanceRepository, $this->calendarEventRepository);
	}

	public function showPayrollForm(Request $request, Employee $employee){
		// Employee $employee = Employee::find($id);
		$start = new \DateTime(sprintf("%d-%02d-%02d", $request->get('year'), $request->get('month'), date('d')));
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = $this->attendanceViewModel->getWorkingMonth($this->settingsRepository, $start);
		$this->viewModel->setModel($employee);
      	$this->viewModel->addData('start', $now);
      	$this->viewModel->addData('end', $next);
      	$this->viewModel->addData('working_days', count($this->attendanceViewModel->workingDays($this->settingsRepository)));

      	return $this->viewModel->setRequest($request)
         ->createShowPayrollForm('POST', 'employee.payroll')
         ->view('pages.employee.showPayroll');		



	}

	public function addLeave(Request $request){
		$employee = Employee::find($request->employee);
		$leaveQuota = $this->viewModel->countRemainLeaveQuota($employee);
		if(gettype($leaveQuota) === 'integer' && $leaveQuota <= 0){
			$request->session()->flash('message', "Employee <strong>{$employee->name}</strong> has no remaining leave quota.");
			$request->session()->flash('alert', "danger");
			return redirect(route('employee.show', ['employee' => $employee->id]));
		}else if(gettype($leaveQuota) === 'string'){
			$request->session()->flash('message', $leaveQuota);
			$request->session()->flash('alert', "danger");
			return redirect(route('employee.show', ['employee' => $employee->id]));
		}else if(gettype($leaveQuota) === 'integer' && $leaveQuota > 0){
			$data = [
				'employee_id' => $employee->id,
				'employee_name' => $employee->name,
				'LeaveQuota' => $leaveQuota,
			];
			// $leaveModel = new Leave();
	
			// $this->leaveViewModel->setModel($leaveModel);
			$this->leaveViewModel->setData($data);
	
			// return $this->leaveViewModel->createForm('POST', 'employee.leave', $employee, null, ['employee' => $employee->id])->view('pages.leave.form');
			return $this->leaveViewModel->createForm('POST', 'leave.store', new Leave())->view('pages.leave.form');

		}

		// return $this->viewModel->createForm('POST', 'employee.leave.store', null, ['employee' => $employee->id])
		//                        ->view('pages.employee.leave_form');

		// return $this->viewModel->view('pages.leave.form');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Employee $employee
	 *
	 * @return \App\Http\ViewModels\EmployeeViewModel|\App\Http\ViewModels\ViewModel|\Illuminate\Http\Response
	 */
	public function show(Request $request, Employee $employee): HttpViewModel|Response|EmployeeViewModel {
		$this->viewModel->setModel($employee);
		// $this->viewModel->countRemainLeaveQuota($employee->id);
		$this->viewModel->payrollCalc($request, $this->settingsRepository, $this->attendanceRepository, $this->calendarEventRepository);

		return $this->viewModel->view('pages.employee.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\Employee $employee
	 *
	 * @return HttpViewModel|ViewModelBase|Response
	 */
	public function edit(Employee $employee): HttpViewModel|Response|ViewModelBase {
		return $this->viewModel->createForm('PUT', 'employee.update', $employee)
		                       ->view('pages.employee.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param EmployeeFormRequest $request
	 * @param \App\Models\Employee $employee
	 *
	 * @return Application|RedirectResponse|Response|Redirector
	 */
	public function update(EmployeeFormRequest $request, Employee $employee): Response|Redirector|Application|RedirectResponse {
		if (!$this->viewModel->update($request, $employee)) {
			return redirect(route('employee.edit', ['employee' => $employee->id]));
		}

		return redirect(route('employee.show', ['employee' => $employee->id]));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Employee $employee
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, Employee $employee) {
        return $this->viewModel->delete($request, $employee);
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function select2List(Request $request): Collection {
		return $this->viewModel->select2List($request);
	}

	public function selectAvailableEmployee(Request $request, EmployeeRepository $employeeRepository): Collection {
		return $this->viewModel->selectAvailableEmployee($request, $employeeRepository);
	}
}
