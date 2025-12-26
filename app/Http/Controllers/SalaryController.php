<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalaryReportFormRequest;
use App\Http\ViewModels\AttendanceViewModel;
use App\Http\ViewModels\SalaryViewModel;
use App\Managers\Form\FormBuilder;
use App\Repositories\Eloquent\AttendanceRepository;
use App\Repositories\Eloquent\CalendarEventRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\SalaryRepository;
use App\Repositories\Eloquent\SettingsRepository;
use Illuminate\Http\Request;


class SalaryController extends Controller {
	private SalaryViewModel $viewModel;

	private SettingsRepository $settingsRepository;

	private CalendarEventRepository $calendarEventRepository;

	private AttendanceRepository $attendanceRepository;

	private EmployeeRepository $employeeRepository;

	public function __construct(SalaryRepository $repository, SettingsRepository $settingsRepository, AttendanceRepository $attendanceRepository,
		CalendarEventRepository $calendarEventRepository, EmployeeRepository $employeeRepository, FormBuilder $builder
	) {
		$this->viewModel = new SalaryViewModel($repository, $builder);
		$this->settingsRepository = $settingsRepository;
		$this->calendarEventRepository = $calendarEventRepository;
		$this->attendanceRepository = $attendanceRepository;
		$this->employeeRepository = $employeeRepository;
	}

	public function report(Request $request) {
		$start = new \DateTime(sprintf("%d-%02d-%02d", $request->get('year'), $request->get('month'), date('d')));
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($this->settingsRepository, $start);

		$this->viewModel->addData('start', $now);
		$this->viewModel->addData('end', $next);
		$this->viewModel->addData('working_days', count($this->viewModel->workingDays($this->settingsRepository)));

		return $this->viewModel->setRequest($request)
		                       ->createReportForm('POST', 'report.salary')
		                       ->view('pages.report.salary.summary');
	}

	public function reports(SalaryReportFormRequest $request) {
		return $this->viewModel->reports($request, $this->employeeRepository, $this->settingsRepository, $this->attendanceRepository, $this->calendarEventRepository);
	}
}
