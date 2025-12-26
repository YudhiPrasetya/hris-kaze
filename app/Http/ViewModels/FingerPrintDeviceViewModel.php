<?php

namespace App\Http\ViewModels;

use App\Http\Forms\FingerPrintDeviceForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\FingerPrintDevice;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class FingerPrintDeviceViewModel extends ViewModelBase{
    public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null)
    {
        parent::__construct($repository, $formBuilder);

        $this->routeBasename = 'fingerprintdevice';
        $this->routeKey = 'fingerprintdevice';
        $this->form = $this->formBuilder->create(FingerPrintDeviceForm::class);

    }

    public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase
    {
        $this->setModel($model);
        $this->form->setMethod($method);
        $this->form->setUrl(route($route, [$this->routeKey => $model->id]));

        return $this;
    }

    public function list(Request $request, ...$columns): Collection{
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

	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$ret = $model->update($fields->toArray());

		return $ret;
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
        $this->form->setRequest($request);
        $this->form->redirectIfNotValid();

        if(!FingerPrintDevice::find($model->id)->forceDelete()){
            $request->session()->flash('message', "Failed to delete <strong>{$model->plat_number}</strong>");
            $request->session()->flash('alert', "danger");
        }else{
            $request->session()->flash('message', "Successfully delete <strong>Data finger print device!</strong>.");
            $request->session()->flash('alert', "success");
        }
        return redirect(route('fingerprintdevice.index'));
	}

	/**
	 * @inheritDoc
	 */
	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$fpDevice = new FingerPrintDevice($fields->toArray());
		$ret = $fpDevice->save();

		return $ret ? $fpDevice : false;
	}


}
