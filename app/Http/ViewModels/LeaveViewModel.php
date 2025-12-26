<?php

namespace App\Http\ViewModels;

use App\Http\Forms\LeaveForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Leave;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class LeaveViewModel extends ViewModelBase{
	/**
	 * LeaveViewModel constructor.
	 *
	 * @param \App\Repositories\EloquentRepositoryInterface $repository
	 * @param \App\Managers\Form\FormBuilder|null $formBuilder
	 *
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'leave';
		$this->routeKey = 'leave';
		$this->form = $this->formBuilder->create(LeaveForm::class);
	}

	/**
	 * @inheritDoc
	 */
	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['leave' => $model->id]));

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		if ($fields->has('attachment_path'))
			$fields->offsetSet('attachment_path', $this->convertImage($request, 'attachment_path'));

        $fields->offsetSet('cut_att_allowance', $this->toBool($fields->get('cut_att_allowance', null)));

		$ret = $model->update($fields->toArray());

		return $ret;
	}

	/**
	 * @inheritDoc
	 */
	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		$this->form->setRequest($request);
        $this->form->redirectIfNotValid();

        if(!Leave::find($model->id)->forceDelete()){
            $request->session()->flash('message', "Failed to delete leave with id <strong>{$model->id}</strong>");
            $request->session()->flash('alert', "danger");
        }else{
            $request->session()->flash('message', "Successfully delete <strong>Data Leave</strong>.");
            $request->session()->flash('alert', "success");
        }
        return redirect(route('leave.index'));
	}

	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		$results = $query->with(['employee:id,name', 'reasonForLeave:id,name'])
		                 ->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					$result['attachment_path'] =
						'<div class="avatar avatar-2xl"><img class="rounded-circle w-100" src="' . $result['attachment_path'] . '" /></div>';
                    $result['leave_date'] = $result['leave_date']->format('Y-m-d');
                    $result['start'] = $result['start']->format('Y-m-d');
                    $result['end'] = $result['end']->format('Y-m-d');

					return $self->addDefaultListActions($result);
				});
			}

			return $item;
		});
	}

	/**
	 * @inheritDoc
	 */
	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		if ($fields->has('attachment_path'))
			$fields->offsetSet('attachment_path', $this->convertImage($request, 'attachment_path'));

        $p = $fields->toArray();
		$leave = new Leave($p);
		$ret = $leave->save();

		return $ret ? $leave : false;
	}

}
