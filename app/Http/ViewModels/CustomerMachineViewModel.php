<?php

namespace App\Http\ViewModels;

use App\Http\Forms\CustomerMachineForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Customer;
use App\Models\CustomerMachine;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class CustomerMachineViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'customer.machine';
		$this->routeKey = 'machine';
		$this->form = $this->formBuilder->create(CustomerMachineForm::class);
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['customer' => $model->id]));

		return $this;
	}

	public function createEditForm(string $method, string $route, ?ModelInterface $customer = null, ?ModelInterface $model = null,
		?string $formClass = null, array $options = []
	): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['customer' => $customer->id, 'machine' => $model->id]));

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

	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$model = new CustomerMachine($fields->toArray());
		$ret = $model->save();

		return $ret ? $model : false;
	}

	public function byCustomer(Request $request, Customer $customer): Collection {
		$self = $this;
		$self->modelPrimaryKey = 'id';
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$results = $customer->machines()->with(['machine:id,name,type'])
		                    ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                    ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self, $customer) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self, $customer) {
					$result['_params'] = ['customer' => $customer->id];

					return $self->addDefaultListActions($result);
				});
			}

			return $item;
		});
	}

	public function selectMachineByCustomer(Request $request, Customer $customer): Collection {
		$search = $request->get('search', null);
		$results = collect([]);
		$items = null;

		if (!empty($search)) {
			$items = $customer->machines()
			                  ->whereHas('machine',
				                  function (Builder $one) use ($search) {
					                  return $one->where('name', 'LIKE', "%$search%");
				                  })
			                  ->get();
		}
		else {
			$items = $customer->machines()
			                  ->select('machine_id', 'serial_number')
			                  ->with(['machine:id,name,type'])
			                  ->get();
		}

		$results->offsetSet('results',
			$items->map(function ($item) {
				$item['machine']['name'] .= sprintf(', %s, %s', $item['machine']['type'], $item['serial_number']);
				return $item['machine'];
			})->toArray());

		return $results;
	}
}
