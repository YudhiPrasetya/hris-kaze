<?php

namespace App\Http\ViewModels;

use App\Http\Forms\AssignmentForm;
use App\Http\Requests\FormRequestInterface;
use App\Libraries\PrettyDateTime;
use App\Managers\Form\FormBuilder;
use App\Models\Assignment;
use App\Models\AssignmentEmployee;
use App\Models\AssignmentPart;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;


class AssignmentViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'assignment';
		$this->routeKey = 'assignment';
		$this->modelPrimaryKey = 'id';
		$this->form = $this->formBuilder->create(AssignmentForm::class);
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['assignment' => $model->id]));

		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		$this->form->setModel($model);
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$ret = $model->update($fields->toArray());

		AssignmentPart::where('assignment_id', '=', $model->id)->forceDelete();
		AssignmentEmployee::where('assignment_id', '=', $model->id)->forceDelete();

		if ($fields->has('parts')) {
			collect($fields['parts'])->each(function ($part) use ($model) {
				$part['assignment_id'] = $model->id;

				return $model->parts()->updateOrCreate($part, ['assignment_id', 'part_name', 'part_type']);
			});
		}

		collect($fields['technicians'])->each(function ($technician) use ($model) {
			$technician['assignment_id'] = $model->id;

			return $model->technicians()->updateOrCreate($technician, ['assignment_id', 'employee_id']);
		});

		return $ret;
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
	}

	/**
	 * @inheritDoc
	 */
	public function new(FormRequestInterface $request): mixed {
		$this->form->setModel(new Assignment());
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$model = new Assignment($fields->toArray());
		$ret = $model->save();

		$model->setStatus('0', 'Created');
		$model->technicians()->createMany($fields['technicians']);
		if ($fields->has('parts')) $model->parts()->createMany($fields['parts']);

		return $ret ? $model : false;
	}

	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		$results = $query->with(['customer:id,name', 'technicians:assignment_id', 'currentStatus:id,name,reason,model_id'])
		                 ->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					$result['service_date'] = $result['service_date']->format('l, d F Y');
					$result['total_worker'] = count($result['technicians']);
					$result['is_chargeable'] = '<span class="badge badge-pill badge-' . ($result['is_chargeable'] ? 'success' : 'danger') . '">' .
					                           ($result['is_chargeable'] ? 'Yes' : 'No') . '</span>';

					return $self->addDefaultListActions($result);
				});
			}

			return $item;
		});
	}

	public function byCustomer(Request $request, Customer $customer) {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$results = $customer->serviceReports()
		                    ->with('customerMachine',
			                    function (HasOne $customerMachine) {
				                    return $customerMachine->select(['id', 'machine_id', 'serial_number'])->with(['machine:id,name,type']);
			                    })
		                    ->with(['customer:id,name', 'currentStatus:model_id,name,reason'])
		                    ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                    ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					$result['_params'] = ['assignment' => $result['id']];
					$result['machine'] = $result['customer_machine']['machine']['name'] . ' &mdash; ' . $result['customer_machine']['machine']['type'];
					$result['is_chargeable'] = '<span class="badge badge-pill badge-' . ($result['is_chargeable'] ? 'success' : 'danger') . '">' .
					                           ($result['is_chargeable'] ? 'Yes' : 'No') . '</span>';
					$result['service_date'] = $result['service_date']->format('l, d F Y');

					return $self->addDefaultListActions($result);
				});
			}

			return $item;
		});
	}

	public function byEmployee(Request $request, Employee $employee) {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$results = $employee->assignments()
		                    ->with('assignment',
			                    function (HasOne $assignment) {
				                    return $assignment
					                    ->with([
						                    'customer:id,name',
						                    'currentStatus:model_id,name,reason',
						                    'parts:assignment_id,part_name,part_type,qty,unit',
						                    'currentStatus:id,reason,model_id'
					                    ])
					                    ->with('customerMachine',
						                    function (HasOne $customerMachine) {
							                    return $customerMachine->select('id', 'serial_number', 'machine_id')->with('machine');
						                    });
			                    })
		                    ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                    ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					$result['_params'] = ['assignment' => $result['assignment']['id']];
					//					$result['machine'] = $result['customer_machine']['machine']['name'] . ' &mdash; ' . $result['customer_machine']['machine']['type'];
					//					$result['is_chargeable'] = '<span class="badge badge-pill badge-' . ($result['is_chargeable'] ? 'success' : 'danger') . '">' .
					//					                           ($result['is_chargeable'] ? 'Yes' : 'No') . '</span>';
					$result['assignment']['service_date'] = (new \DateTime($result['assignment']['service_date']))->format('l, d F Y');

					return $self->addDefaultListActions($result, 'destroy', 'edit');
				});
			}

			return $item;
		});
	}
}
