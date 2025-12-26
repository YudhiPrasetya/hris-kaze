<?php

namespace App\Http\ViewModels;

use App\Http\Forms\AnnualLeaveForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Employee;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;


class AnnualLeaveViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'annual.leave';
		$this->routeKey = 'annual.leave';
		$this->form = $this->formBuilder->create(AnnualLeaveForm::class);
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['annual' => $model->id]));

		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		// TODO: Implement update() method.
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
	}

	public function new(FormRequestInterface $request): mixed {
		// TODO: Implement new() method.
	}

	public function selectByEmployee(Request $request, Employee $employee) {
		$search = $request->get('search', null);
		$results = collect([]);
		$items = null;

		if (!empty($search)) {
			$items = $employee->annualLeaves()
			                  ->select(['id', 'no'])
			                  ->orWhere('no', 'LIKE', "%$search%")
			                  ->orWhere('year', 'LIKE', "%$search%")
			                  ->whereNull('used_at')
			                  ->get();
		}
		else {
			$items = $employee->annualLeaves()
			                  ->select(['id', 'no'])
			                  ->whereNull('used_at')
			                  ->get();
		}

		$results->offsetSet('results', $items->map(function($item) {
			return ['id' => $item['id'], 'name' => Str::upper($item['no'])];
		}));

		return $results;
	}
}
