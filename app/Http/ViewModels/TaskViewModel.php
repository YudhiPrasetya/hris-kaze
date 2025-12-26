<?php

namespace App\Http\ViewModels;

use App\Http\Forms\TaskForm;
use App\Http\Requests\FormRequestInterface;
use App\Libraries\PrettyDateTime;
use App\Managers\Form\FormBuilder;
use App\Models\Employee;
use App\Models\ModelInterface;
use App\Models\Task;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class TaskViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'task';
		$this->routeKey = 'task';
		$this->form = $this->formBuilder->create(TaskForm::class);
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['task' => $model->id]));

		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$ret = $model->update($fields->toArray());

		return $ret;
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
	}

	/**
	 * @inheritDoc
	 */
	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$task = new Task($fields->toArray());
		$ret = $task->save();
		$task->setStatus(0, 'Unconfirmed');

		return $ret ? $task : false;
	}

	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		$results = $query->with(['employee:id,name,profile_photo_path', 'priority:id,name,level', 'currentStatus:id,name,reason,model_id'])
		                 ->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					$result['dateline'] = $result['dateline']->format('l, d F Y');
					$result['priority']['name'] = $self->getPriorityColor($result['priority'], true);
					$result['assign_to'] = $self->createLink($result['employee']['name'], route('employee.show', ['employee' => $result['employee']['id']]));
					return $self->addDefaultListActions($result);
				});
			}

			return $item;
		});
	}

	private function getPriorityColor(array $priority, bool $detail = false) {
		$color = '';
		$icon = '';

		switch ($priority['level']) {
			case 1:
				$color = 'text-blue';
				$icon = 'fad fa-comment-alt-exclamation';
				break;
			case 2:
				$color = 'text-green-blue';
				$icon = 'fad fa-lightbulb-exclamation';
				break;
			case 3:
				$color = 'text-green';
				$icon = 'fad fa-exclamation';
				break;
			case 4:
				$color = 'text-red-green';
				$icon = 'fad fa-exclamation-triangle';
				break;
			case 5:
				$color = 'text-red';
				$icon = 'fad fa-exclamation-circle';
				break;
		}

		if ($detail) return '<span class="'.$color.'"><i class="'.$icon.' mr-2"></i>' . $priority['name'] . '</span>';
		else return '<span data-toggle="tooltip" title="' . $priority['name'] . '"><i class="'.$icon.' mr-2 '.$color.'"></i></span>';
	}

	public function getButton(array $status, $task): Collection {
		return match ($status['name']) {
			'0' => collect([
				'confirm' => [
					'icon'    => 'fad fa-thumbs-up',
					'attr'    => [
						'class' => 'btn btn-sm btn-falcon-default',
						'href'  => route('task.employee.confirm', ['task' => $task])
					],
					'type'    => 'a',
					'tooltip' => 'Confirm',
				]
			]),
			'1' => collect([
				'done' => [
					'icon'    => 'fad fa-check',
					'attr'    => [
						'class' => 'btn btn-sm btn-falcon-success',
						'href'  => route('task.employee.done', ['task' => $task])
					],
					'type'    => 'a',
					'tooltip' => 'Done',
				]
			]),
			default => collect([]),
		};
	}

	public function byEmployee(Request $request, Employee $employee) {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request);
		$results = $query->with(['employee:id,name', 'priority:id,name,level', 'currentStatus:id,name,reason,model_id'])
		                 ->where('employee_id', '=', $employee->id)
		                 ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					$result['dateline'] = $result['dateline']->format('l, d F Y');
					$result['priority']['name'] = $self->getPriorityColor($result['priority']);
					$result['at'] = PrettyDateTime::parse($result['created_at']);
					//$result['statuses'] = $result['statuses'][0];
					$result = $self->addDefaultListActions($result, 'destroy', 'edit');
					$actions = $self->getButton($result['current_status'], $result['id'])->merge($result->get('actions'));
					$result['actions'] = $actions;

					return $result;
				});
			}

			return $item;
		});
	}
}
