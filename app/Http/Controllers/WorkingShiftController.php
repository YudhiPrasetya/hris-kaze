<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkingShiftFormRequest;
use App\Http\ViewModels\WorkingShiftViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\WorkingShift;
use App\Repositories\Eloquent\WorkingShiftRepository;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Http\ViewModels\ViewModelBase;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
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
	public function index(): WorkingShiftViewModel {
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
	 * @param \App\Http\Requests\WorkingShiftFormRequest $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function store(WorkingShiftFormRequest $request): HttpViewModel|WorkingShiftViewModel|Redirector|RedirectResponse|Application {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('workingshift.index'));
		}

		return $this->create();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\WorkingShift $workingshift
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function show(WorkingShift $workingshift) {
		return redirect(route('workingshift.index'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\WorkingShift $workingshift
	 *
	 * @return HttpViewModel|ViewModelBase|Response
	 */
	public function edit(WorkingShift $workingshift): HttpViewModel|Response|ViewModelBase {
		return $this->viewModel->createForm('PUT', 'workingshift.update', $workingshift)->view('pages.working-shift.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param WorkingShiftFormRequest $request
	 * @param \App\Models\WorkingShift  $workingshift
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function update(WorkingShiftFormRequest $request, WorkingShift $workingshift): Response|Redirector|Application|RedirectResponse {
		if (!$this->viewModel->update($request, $workingshift)) {
			return redirect(route('workingshift.edit', ['workingshift' => $workingshift->id]));
		}

		return redirect(route('workingshift.index'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\WorkingShift $workingshift
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function destroy(Request $request, WorkingShift $workingshift) {
		$this->viewModel->delete($request, $workingshift);

		return redirect(route('workingshift.index'));
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}
}
