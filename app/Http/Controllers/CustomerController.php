<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerFormRequest;
use App\Http\Requests\FormRequestInterface;
use App\Http\ViewModels\CustomerViewModel;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Http\ViewModels\ViewModelBase;
use App\Managers\Form\FormBuilder;
use App\Models\Customer;
use App\Repositories\Eloquent\CustomerRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class CustomerController extends Controller {
	private CustomerViewModel $viewModel;

	public function __construct(CustomerRepository $repository, FormBuilder $builder) {
		$this->viewModel = new CustomerViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return CustomerViewModel|HttpViewModel
	 */
	public function index(): HttpViewModel|CustomerViewModel {
		return $this->viewModel->view('pages.customer.list');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param CustomerFormRequest $request
	 *
	 * @return Application|RedirectResponse|Response|Redirector|ViewModelBase
	 */
	public function store(CustomerFormRequest $request): Response|Redirector|RedirectResponse|Application|ViewModelBase {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('customer.show', ['customer' => $model->id]));
		}

		return $this->create();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return HttpViewModel|ViewModelBase
	 */
	public function create() {
		return $this->viewModel->createForm('POST', 'customer.store', new Customer())
		                       ->view('pages.customer.form');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Customer $customer
	 *
	 * @return \App\Http\ViewModels\CustomerViewModel|\App\Http\ViewModels\ViewModel
	 */
	public function show(Customer $customer) {
		return $this->viewModel->setModel($customer)->view('pages.customer.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\Customer $customer
	 *
	 * @return HttpViewModel|ViewModelBase
	 */
	public function edit(Customer $customer): HttpViewModel|ViewModelBase {
		return $this->viewModel->createForm('PUT', 'customer.update', $customer)
		                       ->view('pages.customer.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\Customer     $customer
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(CustomerFormRequest $request, Customer $customer) {
		if (!$this->viewModel->update($request, $customer)) {
			return redirect(route('customer.edit', ['customer' => $customer->id]));
		}

		 return redirect(route('customer.show', ['customer' => $customer->id]));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Customer $customer
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Customer $customer) {
		//
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}
}
