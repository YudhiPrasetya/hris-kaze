<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceFormRequest;
use App\Http\Requests\AttendanceReportFormRequest;
use App\Http\ViewModels\AttendanceViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\Attendance;
use App\Models\Employee;
use App\Repositories\Eloquent\AttendanceRepository;
use App\Repositories\Eloquent\SettingsRepository;
use Illuminate\Http\Request;


class AttendanceController extends Controller {
	private AttendanceViewModel $viewModel;

	private SettingsRepository $settingsRepository;

	public function __construct(AttendanceRepository $repository, SettingsRepository $settingsRepository, FormBuilder $builder) {
		$this->viewModel = new AttendanceViewModel($repository, $builder);
		$this->settingsRepository = $settingsRepository;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \App\Http\ViewModels\AttendanceViewModel|\App\Http\ViewModels\ViewModel
	 */
	public function index() {
		return $this->viewModel->view('pages.attendance.list');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function store(AttendanceFormRequest $request) {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('attendance.index'));
		}

		return $this->create();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
	 */
	public function create(Request $request) {
		$this->viewModel->setDate($request->get('date', date('Y-m-d')));

		return $this->viewModel->createForm('POST', 'attendance.store', new Attendance())
		                       ->view('pages.attendance.form');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Attendance $attendance
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Attendance $attendance) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\Attendance $attendance
	 *
	 * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
	 */
	public function edit(Attendance $attendance) {
		return $this->viewModel->createForm('PUT', 'attendance.update', $attendance)
		                       ->view('pages.attendance.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\Attendance $attendance
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function update(AttendanceFormRequest $request, Attendance $attendance) {
		if (!$this->viewModel->update($request, $attendance)) {
			return redirect(route('attendance.edit', ['attendance' => $attendance->id]));
		}

		return redirect(route('attendance.index'));
	}

	/**
	 * @throws \Exception
	 */
	public function report(Request $request) {
		$start = new \DateTime(sprintf("%d-%02d-%02d", $request->get('year'), $request->get('month'), date('d')));
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($this->settingsRepository, $start);
		//clocK($prev, $now, $next, $cutoffDateStart, $cutoffDateEnd);

		$this->viewModel->addData('start', $now);
		$this->viewModel->addData('end', $next);
		$this->viewModel->addData('working_days', count($this->viewModel->workingDays($this->settingsRepository)));

		return $this->viewModel->setRequest($request)
		                       ->createReportForm('POST', 'report.attendance')
		                       ->view('pages.report.attendance.summary');
	}

	public function reportDetail(Request $request, Employee $employee) {
		$this->viewModel->setModel($employee);
		$this->viewModel->addData('start', $request->get('start', date('Y-m-d')));

		return $this->viewModel->view('pages.report.attendance.detail');
	}

	public function getReportDetail(Request $request, Employee $employee) {
		return $this->viewModel->getReportDetailByEmployee($request, $employee, $this->settingsRepository);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Attendance $attendance
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Attendance $attendance) {
		//
	}

	public function list(Request $request) {
		return $this->viewModel->list($request);
	}

	public function byEmployee(Request $request, Employee $employee) {
		return $this->viewModel->byEmployee($request, $employee);
	}

	public function reports(AttendanceReportFormRequest $request) {
		return $this->viewModel->reports($request, $this->settingsRepository);
	}

	public function reportByEmployee(Request $request, Employee $employee) {
		return $this->viewModel->reportByEmployee($request, $employee);
	}

	public function downloadEmployeeReport(Request $request, Employee $employee) {
		return $this->viewModel->exportExcel($request, $employee, $this->settingsRepository);
	}
}
