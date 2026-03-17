<?php

namespace App\Http\ViewModels;

use App\Http\Forms\OvertimeForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use App\Repositories\Eloquent\SettingsRepository;
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
                    // $result['overtime'] = $result['end']->format('H:i:s');

					return $self->addDefaultListActions($result, 'edit', 'destroy');
				});
			}
			return $item;
		});
	}

	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		// $idEmployee = $fields->get('id_employee');
		// $otDate = $fields->get('overtime_date');
		// $start = $fields->get('start');
		// $end = $fields->get('end');
		// $overtime = $fields->get('overtime');
		// $overtimeDuration = $fields->get('overtime_duration');
		// dd($fields);

		// $att = Attendance::where('employee_id', $idEmployee)->where('at', $otDate)->first();
		// dd($att);
		// if($att->count() > 0){
			// if ($fields->has('status'))
			// 	$fields->offsetSet('status', 0);

		// $att = new Attendance([
		// 	'id' => $fields->get('id_attendance'),
		// 	'overtime_confirmed' => 1
		// ]);
		// $att->update();

		$att = Attendance::find($fields->get('id_attendance'));
		$att->update([
			'overtime_confirmed' => 1
		]);
	
		$o = $fields->toArray();
		$ot = new Overtime($o);
		$ret = $ot->save();
		if($ret){
			return redirect()->route('ot.index')->with('message', 'Overtime request successfully created.')
							 ->with('alert', 'success');
		}
		return false;


		// }
		// return redirect('overtime.create');
		// return false;
	}

	public static function getOvertimeTotalHours(SettingsRepository $settingsRepository, \DateTime $start): int{
      $cutoff = $settingsRepository->findOneBySectionAndKey('attendance', 'cutoff');
      $now = clone($start);

      if ($cutoff->value !== 'end_of_month') {
         //if ((int)date('d') < (int)$cutoff->value)
         $now = $now->sub(new \DateInterval('P1M'));
      }

      $year = $now->format('Y');
      $month = $now->format('m');
      $cutoffDateStart = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value + 1, 2, '0', STR_PAD_LEFT);
      $cutoffDateEnd = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value, 2, '0', STR_PAD_LEFT);
      $prev = new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateStart)));
      $next = (new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateEnd))))->add(new \DateInterval('P1M'));
      if ($cutoff->value === 'end_of_month') $next = $next->sub(new \DateInterval('P1D'));
	  
	  $overtimes = Overtime::whereBetween('overtime_date', [$prev, $next])->get()->sum('overtime_duration');

	  return $overtimes;
	}
}
