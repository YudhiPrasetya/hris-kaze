<?php

namespace App\Http\Controllers;

use App\Http\Requests\MachineFormRequest;
use App\Http\ViewModels\EmployeeViewModel;
use App\Http\ViewModels\MachineViewModel;
use App\Http\ViewModels\ViewModelBase;
use App\Http\ViewModels\ViewModel;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\Machine;
use App\Repositories\Eloquent\MachineRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class MachineController extends Controller {
	private MachineViewModel $viewModel;

	public function __construct(MachineRepository $repository, FormBuilder $builder) {
		$this->viewModel = new MachineViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response|MachineViewModel
	 */
	public function index(): Response|MachineViewModel {
		return $this->viewModel->view('pages.machine.list');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param MachineFormRequest $request
	 *
	 * @return HttpViewModel|Response|Redirector|Application|MachineViewModel|RedirectResponse
	 */
	public function store(MachineFormRequest $request): HttpViewModel|Response|Redirector|Application|MachineViewModel|RedirectResponse {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			// return redirect(route('machine.show', ['machine' => $model->id]));
			return redirect(route('machine.index'));
		}

		return $this->create();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return EmployeeViewModel|ViewModel
	 */
	public function create(): HttpViewModel|EmployeeViewModel {
		return $this->viewModel->createForm('POST', 'machine.store', new Machine())
		                       ->view('pages.machine.form');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Machine $machine
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Machine $machine) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\Machine $machine
	 *
	 * @return HttpViewModel|ViewModelBase
	 */
	public function edit(Machine $machine): HttpViewModel|ViewModelBase {
		return $this->viewModel->createForm('PUT', 'machine.update', $machine)
		                       ->view('pages.machine.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param MachineFormRequest $request
	 * @param \App\Models\Machine      $machine
	 *
	 * @return Application|RedirectResponse|Redirector
	 */
	public function update(MachineFormRequest $request, Machine $machine): Redirector|Application|RedirectResponse {
		if (!$this->viewModel->update($request, $machine)) {
			return redirect(route('machine.edit', ['machine' => $machine->id]));
		}

		// return redirect(route('machine.show', ['machine' => $machine->id]));
		return redirect(route('machine.index'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Machine $machine
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Machine $machine) {
		//
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}
}
