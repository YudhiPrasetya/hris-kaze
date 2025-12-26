<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkingShiftFormRequest;
use App\Http\ViewModels\AnnualLeaveViewModel;
use App\Http\ViewModels\AssignmentViewModel;
use App\Http\ViewModels\WorkingShiftViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\AnnualLeave;
use App\Models\Employee;
use App\Models\WorkingShift;
use App\Repositories\Eloquent\AnnualLeaveRepository;
use App\Repositories\Eloquent\AssignmentRepository;
use App\Repositories\Eloquent\WorkingShiftRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class WorkingShiftController extends Controller {
	private WorkingShiftViewModel $viewModel;

	public function __construct(WorkingShiftRepository $repository, FormBuilder $builder) {
		$this->viewModel = new WorkingShiftViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return $this->viewModel->view('pages.working-shift.list');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
	 */
	public function create() {
		return $this->viewModel->createForm('POST', 'workingshift.store', new WorkingShift())
		                       ->view('pages.working-shift.form');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function store(WorkingShiftFormRequest $request) {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('workingshift.index'));
		}

		return $this->create();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\WorkingShift $workingShift
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function show(WorkingShift $workingShift) {
		return redirect(route('workingshift.index'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\AnnualLeave $annualLeave
	 *
	 * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
	 */
	public function edit(WorkingShift $workingShift) {
		return $this->viewModel->createForm('PUT', 'workingshift.update', $workingShift)
		                       ->view('pages.working-shift.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\AnnualLeave  $annualLeave
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function update(Request $request, WorkingShift $workingShift) {
		if (!$this->viewModel->update($request, $workingShift)) {
			return redirect(route('workingshift.edit', ['workingshift' => $workingShift->id]));
		}

		return redirect(route('workingshift.index'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\AnnualLeave $annualLeave
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function destroy(Request $request, WorkingShift $workingShift) {
		$this->viewModel->delete($request, $workingShift);

		return redirect(route('workingshift.index'));
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}
}
