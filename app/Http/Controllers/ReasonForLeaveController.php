<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReasonForLeaveFormRequest;
use App\Http\ViewModels\ReasonForLeaveViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\JobTitle;
use App\Repositories\Eloquent\ReasonForLeaveRepository;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Http\ViewModels\ViewModelBase;
use App\Models\ReasonForLeave;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class ReasonForLeaveController extends Controller{
    private ReasonForLeaveViewModel $viewModel;

	public function __construct(ReasonForLeaveRepository $repository, FormBuilder $builder) {
		$this->viewModel = new ReasonForLeaveViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return $this->viewModel->view('pages.reason-for-leave.list');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
	 */
	public function create() {
		return $this->viewModel->createForm('POST', 'reasonforleave.store', new ReasonForLeave())
		                       ->view('pages.reason-for-leave.form');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \App\Http\Requests\ReasonForLeaveFormRequest $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function store(ReasonForLeaveFormRequest $request): HttpViewModel|ReasonForLeaveViewModel|Redirector|RedirectResponse|Application{
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('reasonforleave.index'));
		}

		return $this->create();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\ReasonForLeave $reasonforleave
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function show(ReasonForLeave $reasonforleave) {
		return redirect(route('reasonforleave.index'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param \App\Models\ReasonForLeave $reasonforleave
	 * @return HttpViewModel|ViewModelBase|Response
	 */
	public function edit(ReasonForLeave $reasonforleave): HttpViewModel|Response|ViewModelBase {
		return $this->viewModel->createForm('PUT', 'reasonforleave.update', $reasonforleave)
		                       ->view('pages.reason-for-leave.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param ReasonForLeaveFormRequest $request
	 * @param \App\Models\ReasonForLeave  $reasonforleave
	 *
     * @return Application|RedirectResponse|Response|Redirector
	 */
	public function update(ReasonForLeaveFormRequest $request, ReasonForLeave $reasonforleave): Response|Redirector|Application|RedirectResponse  {
		if (!$this->viewModel->update($request, $reasonforleave)) {
			return redirect(route('reasonforleave.edit', ['reasonforleave' => $reasonforleave->id]));
		}

		return redirect(route('reasonforleave.index'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function destroy(Request $request, ReasonForLeave $reasonforleave) {
		$this->viewModel->delete($request, $reasonforleave);

		return redirect(route('reasonforleave.index'));
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}
}
