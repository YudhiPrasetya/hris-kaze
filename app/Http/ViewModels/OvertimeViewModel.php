<?php

namespace App\Http\ViewModels;

use App\Http\Forms\OvertimeForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Overtime;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class OvertimeViewModel extends ViewModelBase{
    /**
     * OvertimeViewModel constructor.
     *
	 * @param \App\Repositories\EloquentRepositoryInterface $repository
	 * @param \App\Managers\Form\FormBuilder|null $formBuilder
	 *
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null)
    {
        parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'ot';
		$this->routeKey = 'ot';
		$this->form = $this->formBuilder->create(OvertimeForm::class);

    }

	/**
	 * @inheritDoc
	 */
	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['ot' => $model->id]));

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();

		$ret = $model->update($fields->toArray());

		return $ret;
	}

	/**
	 * @inheritDoc
	 */
	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		$this->form->setRequest($request);
        $this->form->redirectIfNotValid();

        if(!Overtime::find($model->id)->forceDelete()){
            $request->session()->flash('message', "Failed to delete overtime with id <strong>{$model->id}</strong>");
            $request->session()->flash('alert', "danger");
        }else{
            $request->session()->flash('message', "Successfully delete <strong>Data Overtime</strong>.");
            $request->session()->flash('alert', "success");
        }
        return redirect(route('ot.index'));
	}

	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		$results = $query->with(['employee:id,name'])
		                 ->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
                    $result['overtime_date'] = $result['overtime_date']->format('Y-m-d');

                    // $result['start'] = $result['start']->format('H:i:s');

                    // $result['end'] = $result['end']->format('H:i:s');

					return $self->addDefaultListActions($result);
				});
			}
			return $item;
		});
	}

	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		if ($fields->has('status'))
			$fields->offsetSet('status', 0);

        $o = $fields->toArray();
		$overtime = new Overtime($o);
		$ret = $overtime->save();

		return $ret ? $overtime : false;
	}
}
