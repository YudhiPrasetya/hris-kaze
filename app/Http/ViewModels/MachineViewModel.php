<?php

namespace App\Http\ViewModels;

use App\Http\Forms\EmployeeForm;
use App\Http\Forms\MachineForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Machine;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class MachineViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'machine';
		$this->routeKey = 'machine';
		$this->form = $this->formBuilder->create(MachineForm::class);
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {

		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['machine' => $model->id]));

		return $this;
	}

	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		$results = $this->getPaginatedList($request, $this->repository, ...$columns);
		$rows = $results->get('rows')->map(function ($result, $key) use ($self) {
			return $self->addDefaultListActions($result, 'show');
		});
		$results->offsetSet('rows', $rows);

		return $results;
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
		$machine = new Machine($fields->toArray());
		$ret = $machine->save();

		return $ret ? $machine : false;
	}
}
