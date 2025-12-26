<?php

namespace App\Http\ViewModels;

use App\Http\Forms\PositionForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\ModelInterface;
use App\Models\Position;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class PositionViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'position';
		$this->routeKey = 'position';
		$this->form = $this->formBuilder->create(PositionForm::class);
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
		// TODO: Implement createForm() method.
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['postion' => $model->id]));

		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		// TODO: Implement update() method.
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$ret = $model->update($fields->toArray());

		return $ret;
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
		if (!Position::find($model->id)->delete()) {
			$request->session()->flash('message', "Failed to delete position <strong>{$model->name}</strong>");
			$request->session()->flash('alert', "danger");
		}
		else {
			$request->session()->flash('message', "Successfully delete position <strong>{$model->name}</strong>.");
			$request->session()->flash('alert', "success");
		}

		return redirect(route('position.index'));
	}

	public function new(FormRequestInterface $request): mixed {
		// TODO: Implement new() method.
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$position = new Position($fields->toArray());
		$ret = $position->save();

		return $ret ? $position : false;
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
}
