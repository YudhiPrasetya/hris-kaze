<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermitFormRequest;

use App\Http\ViewModels\PermissionViewModel;
use App\Http\ViewModels\PermitViewModel;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Http\ViewModels\ViewModelBase;

use App\Managers\Form\FormBuilder;

use App\Models\Permit;
use App\Models\Employee;
use App\Models\ReasonForLeave;

use App\Repositories\Eloquent\PermitRepository;

use Illuminate\Contracts\Foundation\Application;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class PermitController extends Controller{
    private PermitViewModel $permitViewModel;

    /**
     * PermitController constructor
     *
     * @param \App\Repositories\Eloquent\PermitRepository $repository
     * @param \App\Managers\Form\FormBuilder $builder
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(PermitRepository $repository, FormBuilder $builder)
    {
        $this->permitViewModel = new PermitViewModel($repository, $builder);
    }

    /**
     * Display a listing of the resource
     *
     * @return Response|PermitViewModel
     */
    public function index(): Response|PermitViewModel{
        return $this->permitViewModel->view('pages.permit.list');
    }

    public function list(Request $request): Collection{
        return $this->permitViewModel->list($request);
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \App\Http\Requests\PermitFormRequest $request
	 *
	 * @return PermitViewModel|ViewModel|Application|RedirectResponse|Redirector
	 * @throws \Exception
	 */
	public function store(PermitFormRequest $request): HttpViewModel|PermitViewModel|Redirector|RedirectResponse|Application {
		$model = $this->permitViewModel->new($request);

		if ($model !== false) {
			return redirect(route('permit.show', ['permit' => $model->id]));
		}

		return $this->create();
	}

    /**
     * Display the specified resource
     *
     * @param \App\Models\Permit $permit
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse:\Illuminate\Routing\Redirector
     */
    public function show(Permit $permit){
        return redirect(route('permit.index'));
    }

    /**
     * Show the form creating a new source
     *
     * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
     */
    public function create(): HttpViewModel|ViewModelBase{
        return $this->permitViewModel->createForm('POST', 'permit.store', new Permit())->view('pages.permit.form');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permit  $permit
     *
     * @return HttpViewModel|ViewModelBase
     */
    public function edit(Permit $permit): HttpViewModel|ViewModelBase {
	    return $this->permitViewModel->createForm('PUT', 'permit.update', $permit)->view('pages.permit.form');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PermitFormRequest $request
     * @param  \App\Models\Permit  $permit
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(PermitFormRequest $request, Permit $permit): Redirector|Application|RedirectResponse {
	    if (!$this->permitViewModel->update($request, $permit)) {
		    return redirect(route('permit.edit', ['permit' => $permit->id]));
	    }
	    return redirect(route('permit.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permit  $permit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Permit $permit)
    {
	    return $this->permitViewModel->delete($request, $permit);
    }
}
