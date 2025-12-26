<?php

namespace App\Http\Controllers;

use App\Http\ViewModels\VehicleViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\Vehicle;
use App\Repositories\Eloquent\VehicleRepository;
use Illuminate\Http\Request;
use App\Http\Requests\VehicleFormRequest;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Models\Machine;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use App\Http\ViewModels\ViewModelBase;
use Illuminate\Support\Collection;


class VehicleController extends Controller
{
	private VehicleViewModel $viewModel;

	public function __construct(VehicleRepository $repository, FormBuilder $builder) {
		$this->viewModel = new VehicleViewModel($repository, $builder);
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response|VehicleViewModel
    {
	    return $this->viewModel->view('pages.vehicle.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
     */
    public function create(): HttpViewModel|ViewModelBase {
	    return $this->viewModel->createForm('POST', 'vehicle.store', new Machine())
	                           ->view('pages.vehicle.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleFormRequest $request): HttpViewModel|Response|Redirector|Application|VehicleViewModel|RedirectResponse
    {
	    $model = $this->viewModel->new($request);

	    if ($model !== false) {
		    // return redirect(route('vehicle.show', ['vehicle' => $model->id]));
		    return redirect(route('vehicle.index'));
	    }

	    return $this->create();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     *
     * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
     */
    public function edit(Vehicle $vehicle): HttpViewModel|ViewModelBase {
	    return $this->viewModel->createForm('PUT', 'vehicle.update', $vehicle)
	                           ->view('pages.vehicle.form');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehicle  $vehicle
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(VehicleFormRequest $request, Vehicle $vehicle): Redirector|Application|RedirectResponse {
	    if (!$this->viewModel->update($request, $vehicle)) {
		    return redirect(route('vehicle.edit', ['vehicle' => $vehicle->id]));
	    }

	    // return redirect(route('vehicle.show', ['vehicle' => $vehicle->id]));
	    return redirect(route('vehicle.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Vehicle $vehicle)
    {
	    return $this->viewModel->delete($request, $vehicle);
    }

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}
}
