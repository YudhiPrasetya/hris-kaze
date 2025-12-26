<?php

namespace App\Http\ViewModels;

use App\Http\Forms\VehicleForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\ModelInterface;
use App\Models\Vehicle;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;


class VehicleViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'vehicle';
		$this->routeKey = 'vehicle';
		$this->form = $this->formBuilder->create(VehicleForm::class);
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
        // dd($model);
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, [$this->routeKey => $model->id]));

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$response = Http::asJson()
		                ->withBasicAuth('eq.petrucci@gmail.com', 'pwd4me_84$')
		                ->withoutVerifying()
		                ->get('http://35.188.167.11:8082/api/devices');

		if ($response->ok()) {
			$fields = $this->getFormFields();
			$data = $fields->toArray();
			$devices = collect($response->object());
			$filtered = $devices->filter(function ($item) use($data) {
				return $item->uniqueId == $data['imei'];
			});
			$success = true;

			if ($filtered->count() == 0) {
				$response = Http::asJson()
				                ->withBasicAuth('eq.petrucci@gmail.com', 'pwd4me_84$')
				                ->withoutVerifying()
				                ->post('http://35.188.167.11:8082/api/devices',
					                [
						                'name'       => $model->plat_number,
						                'uniqueId'   => $data['imei'],
						                'model'      => $model->type,
						                'attributes' => [
							                'plat_number' => $model->plat_number,
						                ],
						                'category'   => 'car',
									]);
				$success = $response->ok();
			}

			if ($success)
				$ret = $model->update($data);
		}
		else {
			$request->session()->flash('message', "Failed to update <strong>{$model->plat_number}</strong>");
			$request->session()->flash('alert', "danger");
		}

		return $ret;
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		$response = Http::asJson()
		                ->withBasicAuth('eq.petrucci@gmail.com', 'pwd4me_84$')
		                ->withoutVerifying()
		                ->delete('http://35.188.167.11:8082/api/devices/' . $model->device_id);

		if ($response->status() == 204) {
			if (!Vehicle::find($model->id)->forceDelete()) {
				$request->session()->flash('message', "Failed to delete <strong>{$model->plat_number}</strong>");
				$request->session()->flash('alert', "danger");
			}
			else {
				$request->session()->flash('message', "Successfully delete <strong>{$model->plat_number}</strong>.");
				$request->session()->flash('alert', "success");
			}
		}

		return redirect(route('vehicle.index'));
	}

	/**
	 * @inheritDoc
	 */
	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$vehicle = new Vehicle($fields->toArray());

		$response = Http::asJson()
		                ->withBasicAuth('eq.petrucci@gmail.com', 'pwd4me_84$')
		                ->withoutVerifying()
		                ->post('http://35.188.167.11:8082/api/devices',
			                [
				                'name'       => $vehicle->plat_number,
				                'uniqueId'   => $vehicle->imei,
				                'model'      => $vehicle->type,
				                'attributes' => [
					                'plat_number' => $vehicle->plat_number,
				                ],
				                'category'   => 'car',
			                ]);

		if ($response->ok()) {
			$vehicle->device_id = $response->object()->id;
			$ret = $vehicle->save();
		}

		return $ret ? $vehicle : false;
	}

	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		$results = $query//->with(['country:iso,name', 'state:id,name', 'city:id,name', 'district:id,name', 'village:id,name', 'position:id,name'])
		->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					return $self->addDefaultListActions($result, 'show');
				});
			}

			return $item;
		});
	}
}
