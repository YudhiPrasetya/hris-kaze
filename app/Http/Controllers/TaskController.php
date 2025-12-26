<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskFormRequest;
use App\Http\ViewModels\TaskViewModel;
use App\Http\ViewModels\ViewModel;
use App\Http\ViewModels\ViewModelBase;
use App\Managers\Form\FormBuilder;
use App\Models\Employee;
use App\Models\Task;
use App\Repositories\Eloquent\TaskRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class TaskController extends Controller {
	private TaskViewModel $viewModel;

	public function __construct(TaskRepository $repository, FormBuilder $builder) {
		$this->viewModel = new TaskViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return TaskViewModel|ViewModel|Response
	 */
	public function index(): ViewModel|Response|TaskViewModel {
		return $this->viewModel->view('pages.task.list');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return Application|RedirectResponse|Redirector|ViewModel|ViewModelBase
	 */
	public function store(TaskFormRequest $request): Application|RedirectResponse|Redirector|ViewModel|ViewModelBase {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('task.show', ['task' => $model->id]));
		}

		return $this->create();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
	 */
	public function create(): ViewModel|ViewModelBase {
		return $this->viewModel->createForm('POST', 'task.store', new Task())
		                       ->view('pages.task.form');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Task $task
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Task $task) {
		return $this->viewModel->setModel($task)->view('pages.task.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\Task $task
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Task $task) {
		return $this->viewModel->createForm('PUT', 'task.update', $task)
		                       ->view('pages.task.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\Task         $task
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(TaskFormRequest $request, Task $task) {
		if (!$this->viewModel->update($request, $task)) {
			return redirect(route('task.edit', ['task' => $task->id]));
		}

		return redirect(route('task.show', ['task' => $task->id]));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Task $task
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Task $task) {
		//
	}

	public function confirm(Request $request, Task $task) {
		$task->setStatus(1, 'Confirmed / On Progress');

		// return redirect(route('task.show', ['task' => $task->id]));
		return $this->viewModel->setModel($task)->view('pages.task.show');
	}

	public function done(Request $request, Task $task) {
		$task->setStatus(2, 'Done');

		return redirect(route('task.show', ['task' => $task->id]));
	}

	public function cancel(Request $request, Task $task) {
		$task->setStatus(-1, 'Cancel');

		return redirect(route('task.show', ['task' => $task->id]));
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}

	public function getByEmployee(Request $request, Employee $employee) {
		return $this->viewModel->byEmployee($request, $employee);
	}
}
