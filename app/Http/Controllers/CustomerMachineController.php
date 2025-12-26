<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerFormRequest;
use App\Http\ViewModels\CustomerMachineViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\Customer;
use App\Models\CustomerMachine;
use App\Models\Machine;
use App\Repositories\Eloquent\AssignmentRepository;
use Illuminate\Http\Request;


class CustomerMachineController extends Controller {
	private CustomerMachineViewModel $viewModel;

	public function __construct(AssignmentRepository $repository, FormBuilder $builder) {
		$this->viewModel = new CustomerMachineViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request, Customer $customer) {
		return $this->viewModel->createForm('POST', 'customer.machine.store', $customer)
		                       ->view('pages.customer.machine.form');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(CustomerFormRequest $request, Customer $customer) {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			// return redirect(route('customer.machine.show', ['assignment' => $model->id]));
			return redirect(route('customer.show', ['customer' => $customer->id]));
		}

		return $this->create($request, $customer);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\CustomerMachine $customerMachine
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(CustomerMachine $customerMachine) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\CustomerMachine $customerMachine
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Customer $customer, CustomerMachine $machine) {
		return $this->viewModel->createEditForm('PUT', 'customer.machine.update', $customer, $machine)
		                       ->view('pages.customer.machine.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request    $request
	 * @param \App\Models\CustomerMachine $customerMachine
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(CustomerFormRequest $request, Customer $customer, CustomerMachine $machine) {
		if (!$this->viewModel->update($request, $machine)) {
			return redirect(route('customer.machine.edit', ['customer' => $customer->id, 'machine' => $machine->id]));
		}

		return redirect(route('customer.show', ['customer' => $customer->id]));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\CustomerMachine $customerMachine
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(CustomerMachine $customerMachine) {
		//
	}

	public function list(Request $request, Customer $customer) {
		return $this->viewModel->byCustomer($request, $customer);
	}

	public function selectMachineByCustomer(Request $request, Customer $customer) {
		return $this->viewModel->selectMachineByCustomer($request, $customer);
	}
}
