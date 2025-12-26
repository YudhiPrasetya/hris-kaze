<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignmentFormRequest;
use App\Http\ViewModels\AssignmentViewModel;
use App\Http\ViewModels\ViewModel;
use App\Http\ViewModels\ViewModelBase;
use App\Managers\Form\FormBuilder;
use App\Models\Assignment;
use App\Models\Customer;
use App\Models\Employee;
use App\Repositories\Eloquent\AssignmentRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class AssignmentController extends Controller {
	private AssignmentViewModel $viewModel;

	public function __construct(AssignmentRepository $repository, FormBuilder $builder) {
		$this->viewModel = new AssignmentViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return AssignmentViewModel|ViewModel
	 */
	public function index() {
		return $this->viewModel->view('pages.assignment.list');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return Application|RedirectResponse|Redirector|Response|ViewModel|ViewModelBase
	 */
	public function store(AssignmentFormRequest $request) {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('assignment.show', ['assignment' => $model->id]));
		}

		return $this->create();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return ViewModel|ViewModelBase
	 */
	public function create() {
		return $this->viewModel->createForm('POST', 'assignment.store', new Employee())
		                       ->view('pages.assignment.form');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Assignment $assignment
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Assignment $assignment) {
		return $this->viewModel->setModel($assignment)->view('pages.assignment.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\Assignment $assignment
	 *
	 * @return ViewModel|ViewModelBase
	 */
	public function edit(Assignment $assignment) {
		return $this->viewModel->createForm('PUT', 'assignment.update', $assignment)
		                       ->view('pages.assignment.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\Assignment   $assignment
	 *
	 * @return Application|RedirectResponse|Redirector
	 */
	public function update(AssignmentFormRequest $request, Assignment $assignment) {
		if (!$this->viewModel->update($request, $assignment)) {
			return redirect(route('assignment.edit', ['assignment' => $assignment->id]));
		}

		return redirect(route('assignment.show', ['assignment' => $assignment->id]));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Assignment $assignment
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Assignment $assignment) {
		//
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}

	public function getByCustomer(Request $request, Customer $customer): Collection {
		return $this->viewModel->byCustomer($request, $customer);
	}

	public function getByEmployee(Request $request, Employee $employee): Collection {
		return $this->viewModel->byEmployee($request, $employee);
	}
}
