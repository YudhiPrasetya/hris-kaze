<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobTitleFormRequest;
use App\Http\ViewModels\JobTitleViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\JobTitle;
use App\Repositories\Eloquent\JobTitleRepository;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Http\ViewModels\ViewModelBase;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class JobTitleController extends Controller {
	private JobTitleViewModel $viewModel;

	public function __construct(JobTitleRepository $repository, FormBuilder $builder) {
		$this->viewModel = new JobTitleViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return $this->viewModel->view('pages.jobtitle.list');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
	 */
	public function create() {
		return $this->viewModel->createForm('POST', 'jobtitle.store', new JobTitle())
		                       ->view('pages.jobtitle.form');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \App\Http\Requests\JobTitleFormRequest $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function store(JobTitleFormRequest $request): HttpViewModel|JobTitleViewModel|Redirector|RedirectResponse|Application{
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('jobtitle.index'));
		}

		return $this->create();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\JobTitle $jobtitle
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function show(JobTitle $jobtitle) {
		return redirect(route('jobtitle.index'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param \App\Models\JobTitle $jobtitle
	 * @return HttpViewModel|ViewModelBase|Response
	 */
	public function edit(JobTitle $jobtitle): HttpViewModel|Response|ViewModelBase {
		return $this->viewModel->createForm('PUT', 'jobtitle.update', $jobtitle)
		                       ->view('pages.jobtitle.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param JobTitleFormRequest $request
	 * @param \App\Models\JobTitle  $jobtitle
	 *
     * @return Application|RedirectResponse|Response|Redirector
	 */
	public function update(JobTitleFormRequest $request, JobTitle $jobtitle): Response|Redirector|Application|RedirectResponse  {
		if (!$this->viewModel->update($request, $jobtitle)) {
			return redirect(route('jobtitle.edit', ['jobtitle' => $jobtitle->id]));
		}

		return redirect(route('jobtitle.index'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function destroy(Request $request, JobTitle $jobtitle) {
		$this->viewModel->delete($request, $jobtitle);

		return redirect(route('jobtitle.index'));
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}
}
