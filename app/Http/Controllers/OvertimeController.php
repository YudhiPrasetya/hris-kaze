<?php

namespace App\Http\Controllers;

use App\Http\Requests\OvertimeFormRequest;

use App\Http\ViewModels\OvertimeViewModel;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Http\ViewModels\ViewModelBase;

use App\Managers\Form\FormBuilder;

use App\Models\Overtime;
use App\Models\Employee;

use App\Repositories\Eloquent\OvertimeRepository;

use Illuminate\Contracts\Foundation\Application;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class OvertimeController extends Controller{
    private OvertimeViewModel $overtimeViewModel;

    /**
     * OvertimeController constructor
     *
     * @param \App\Repositories\Eloquent\OvertimeRepository $repository
     * @param \App\Managers\Form\FormBuilder $builder
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(OvertimeRepository $repository, FormBuilder $builder)
    {
        $this->overtimeViewModel = new OvertimeViewModel($repository, $builder);
    }

    /**
     * Display a listing of the resource
     *
     * @return Response|OvertimeViewModel
     */
    public function index(): Response|OvertimeViewModel{
        return $this->overtimeViewModel->view('pages.overtime.list');
    }

    public function list(Request $request): Collection{
        return $this->overtimeViewModel->list($request);
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \App\Http\Requests\OvertimeFormRequest $request
	 *
	 * @return OvertimeViewModel|ViewModel|Application|RedirectResponse|Redirector
	 * @throws \Exception
	 */
	public function store(OvertimeFormRequest $request): HttpViewModel|OvertimeViewModel|Redirector|RedirectResponse|Application {
		$model = $this->overtimeViewModel->new($request);

		if ($model !== false) {
			return redirect(route('ot.show', ['ot' => $model->id]));
		}

		return $this->create();
	}

    /**
     * Display the specified resource
     *
     * @param \App\Models\Overtime $overtime
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse:\Illuminate\Routing\Redirector
     */
    public function show(Overtime $overtime){
        $this->overtimeViewModel->setModel($overtime);

        // return redirect(route('ot.index'));
        return $this->overtimeViewModel->view('pages.overtime.show');
    }

    /**
     * Show the form creating a new source
     *
     * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
     */
    public function create(): HttpViewModel|ViewModelBase{
        // $ot = new Overtime();
        return $this->overtimeViewModel->createForm('POST', 'ot.store', new Overtime())->view('pages.overtime.form');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Overtime  $overtime
     *
     * @return HttpViewModel|ViewModelBase
     */
    public function edit(Overtime $overtime): HttpViewModel|ViewModelBase {
	    return $this->overtimeViewModel->createForm('PUT', 'ot.update', $overtime)->view('pages.overtime.form');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OvertimeFormRequest $request
     * @param  \App\Models\Overtime  $overtime
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(OvertimeFormRequest $request, Overtime $overtime): Redirector|Application|RedirectResponse {
	    if (!$this->overtimeViewModel->update($request, $overtime)) {
		    return redirect(route('ot.edit', ['ot' => $overtime->id]));
	    }
	    return redirect(route('ot.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Overtime  $overtime
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Overtime $overtime)
    {
	    return $this->overtimeViewModel->delete($request, $overtime);
    }
}
