<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveFormRequest;

use App\Http\ViewModels\LeaveViewModel;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Http\ViewModels\ViewModelBase;

use App\Managers\Form\FormBuilder;

use App\Models\Leave;

use App\Repositories\Eloquent\LeaveRepository;

use Illuminate\Contracts\Foundation\Application;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class LeaveController extends Controller{
    private LeaveViewModel $leaveViewModel;

    /**
     * LeaveController constructor
     *
     * @param \App\Repositories\Eloquent\LeaveRepository $repository
     * @param \App\Managers\Form\FormBuilder $builder
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(LeaveRepository $repository, FormBuilder $builder)
    {
        $this->leaveViewModel = new LeaveViewModel($repository, $builder);
    }

    /**
     * Display a listing of the resource
     *
     * @return Response|LeaveViewModel
     */
    public function index(): Response|LeaveViewModel{
        return $this->leaveViewModel->view('pages.leave.list');
    }

    public function list(Request $request): Collection{
        return $this->leaveViewModel->list($request);
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \App\Http\Requests\LeaveFormRequest $request
	 *
	 * @return LeaveViewModel|ViewModel|Application|RedirectResponse|Redirector
	 * @throws \Exception
	 */
	public function store(LeaveFormRequest $request): HttpViewModel|LeaveViewModel|Redirector|RedirectResponse|Application {
		$model = $this->leaveViewModel->new($request);

		if ($model !== false) {
			return redirect(route('leave.show', ['leave' => $model->id]));
		}

		return $this->create();
	}

    /**
     * Display the specified resource
     *
     * @param \App\Models\Leave $leave
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse:\Illuminate\Routing\Redirector
     */
    // public function show(Leave $leave){
    //     return redirect(route('leave.index'));
    // }

    /**
     * Show the form creating a new source
     *
     * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
     */
    public function create(): HttpViewModel|ViewModelBase{
        return $this->leaveViewModel->createForm('POST', 'leave.store', new Leave())->view('pages.leave.form');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     *
     * @return HttpViewModel|ViewModelBase
     */
    public function edit(Leave $leave): HttpViewModel|ViewModelBase {
	    return $this->leaveViewModel->createForm('PUT', 'leave.update', $leave)->view('pages.leave.form');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LeaveFormRequest $request
     * @param  \App\Models\Leave  $leave
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(LeaveFormRequest $request, Leave $leave): Redirector|Application|RedirectResponse {
	    if (!$this->leaveViewModel->update($request, $leave)) {
		    return redirect(route('leave.edit', ['leave' => $leave->id]));
	    }
	    return redirect(route('leave.index'));
    }

    /**
     * Display the specified resource
     *
     * @param \App\Models\Leave $leave
     *
     * @return \App\Http\ViewModels\LeaveViewModel|\App\Http\ViewModels\ViewModel|\Illuminate\Http\Response
     */
    public function show(Leave $leave): HttpViewModel|Response|LeaveViewModel{

        $this->leaveViewModel->setModel($leave);

        return $this->leaveViewModel->view('pages.leave.show');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Leave $leave)
    {
	    return $this->leaveViewModel->delete($request, $leave);
    }
}
