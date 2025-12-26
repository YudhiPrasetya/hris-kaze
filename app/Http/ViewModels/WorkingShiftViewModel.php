<?php

namespace App\Http\ViewModels;

use App\Http\Forms\WorkingShiftForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Employee;
use App\Models\ModelInterface;
use App\Models\WorkingShift;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class WorkingShiftViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'workingshift';
		$this->routeKey = 'workingshift';
		$this->form = $this->formBuilder->create(WorkingShiftForm::class);
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['annual' => $model->id]));

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
		if (!WorkingShift::find($model->id)->delete()) {
			$request->session()->flash('message', "Failed to delete working shift <strong>{$model->start} &dash; {$model->end}</strong>");
			$request->session()->flash('alert', "danger");
		}
		else {
			$request->session()->flash('message', "Successfully delete working shift <strong>{$model->start} &dash; {$model->end}</strong>.");
			$request->session()->flash('alert', "success");
		}

		return redirect(route('working.shift'));
	}

	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$attendance = new WorkingShift($fields->toArray());
		$ret = $attendance->save();

		return $ret ? $attendance : false;
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
