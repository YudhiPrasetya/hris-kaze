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


class EmployeeController extends Controller {
	private EmployeeViewModel $viewModel;
	private SettingsRepository $settingsRepository;
	private AttendanceRepository $attendanceRepository;
	private CalendarEventRepository $calendarEventRepository;

	/**
	 * EmployeeController constructor.
	 *
	 * @param \App\Repositories\Eloquent\EmployeeRepository $repository
	 * @param \App\Managers\Form\FormBuilder $builder
	 *
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function __construct(EmployeeRepository $repository, SettingsRepository $settingsRepository, AttendanceRepository $attendanceRepository, CalendarEventRepository $calendarEventRepository, FormBuilder $builder) {
		$this->viewModel = new EmployeeViewModel($repository, $builder);
		$this->settingsRepository = $settingsRepository;
		$this->attendanceRepository = $attendanceRepository;
		$this->calendarEventRepository = $calendarEventRepository;
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

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Employee $employee
	 *
	 * @return \App\Http\ViewModels\EmployeeViewModel|\App\Http\ViewModels\ViewModel|\Illuminate\Http\Response
	 */
	public function show(Request $request, Employee $employee): HttpViewModel|Response|EmployeeViewModel {
		$this->viewModel->setModel($employee);
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
