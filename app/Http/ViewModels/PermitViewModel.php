<?php

namespace App\Http\ViewModels;

use App\Http\Forms\PermitForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Permit;
use App\Models\Attendance;
use App\Models\ReasonForLeave;
use App\Models\ModelInterface;
use App\Repositories\Eloquent\PermitRepository;
use App\Repositories\Eloquent\ReasonForLEaveRepository;
use App\Repositories\EloquentRepositoryInterface;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Models\CalendarEvent;
class PermitViewModel extends ViewModelBase{
	/**
	 * PermitViewModel constructor.
	 *
	 * @param \App\Repositories\EloquentRepositoryInterface $repository
	 * @param \App\Managers\Form\FormBuilder|null $formBuilder
	 *
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'permit';
		$this->routeKey = 'permit';
		$this->form = $this->formBuilder->create(PermitForm::class);
	}

	/**
	 * @inheritDoc
	 */
	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['permit' => $model->id]));

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

        if(!Permit::find($model->id)->forceDelete()){
            $request->session()->flash('message', "Failed to delete permit with id <strong>{$model->id}</strong>");
            $request->session()->flash('alert', "danger");
        }else{
            $request->session()->flash('message', "Successfully delete <strong>Data Permit</strong>.");
            $request->session()->flash('alert', "success");
        }
        return redirect(route('permit.index'));
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
					$result['attachment_path'] = '<div class="avatar avatar-2xl"><img class="rounded-circle w-100" src="' . $result['attachment_path'] . '" /></div>';

					$permitType = $result['permit_type'];
					switch($permitType){
						case "2":
							$result['permit_type'] = "Sick (Sakit)";
							break;
						case "3":
							$result['permit_type'] = "Business Trip (Perjalanan Bisnis)";
							break;
						case "4":
							$result['permit_type'] = "Permit (Izin)";
							break;
					}

                    $result['permit_date'] = $result['permit_date']->format('Y-m-d');
                    $result['start'] = $result['start']->format('Y-m-d');
                    $result['end'] = $result['end']->format('Y-m-d');

					return $self->addDefaultListActions($result, 'edit', 'destroy');
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

		$fields->offsetSet('cut_att_premium', $this->toBool($fields->get('cut_att_premium')));
		$fields->offsetSet('cut_att_salary', $this->toBool($fields->get('cut_att_salary')));
		
		$p = $fields->toArray();
		// dd($p);
		$permit = new Permit($p);
		$ret = $permit->save();
		
		// Add sick, businnes trip or permit attendances
		$employeeId = $fields->get('id_employee');
		$permitType = $fields->get('permit_type');
		$dateStartPermit = new DateTime($fields->get('start'));
		$dateEndPermit = new DateTime($fields->get('end'));
		// $yearNow = new DateTime()->format('Y');

		// $eventsCalendar = CalendarEvent::where('start_date', '>=', $dateStartPermit)
		// 	->where('start_date', '<=', $dateEndPermit)
		// 	->where('recurring', '=', 0)
		// 	->get()->pluck('start_date')->toArray();

		$eventsCalendar = $this->nationalEvents($dateStartPermit, $dateEndPermit);

		while($dateStartPermit <= $dateEndPermit){
			$dayOfWeek = $dateStartPermit->format('w');
			$checkNotInEventCalendar = in_array($dateStartPermit->format('Y-m-d'), $eventsCalendar);
			if($checkNotInEventCalendar === false && ($dayOfWeek != "0" && $dayOfWeek != "6")){
				$attPermit = new Attendance([
					'employee_id' => $employeeId,
					'attendance_reason_id' => $permitType,
					'at' => $dateStartPermit,
					'cut_att_premium' => $fields->get('cut_att_premium'),
					'cut_att_salary' => $fields->get('cut_att_salary')
				]);
				$attPermit->save();
			}
			$dateStartPermit->modify('+1 day');
		}
		// $permitDays = (new DateTime($dateStartPermit))->diff(new DateTime($dateEndPermit))->d + 1;

		// for($x = 0; $x < $permitDays; $x++){
		// 	$datePermit = new DateTime($dateStartPermit);
		// 	$attPermit = new Attendance([
		// 		'employee_id' => $employeeId,
		// 		'attendance_reason_id' => $permitType,
		// 		'at' => $datePermit->modify('+'.$x.' day')
		// 	]);
		// 	$attPermit->save();
		// }		

		return $ret ? $permit : false;
	}

	private function nationalEvents(DateTime $start, DateTime $end) {
		$months = [];
		$years = [];
		$s = (int)$start->format('n');
		$sy = (int)$start->format('Y');
		$e = (int)$end->format('n');

		while (1) {
			$months[] = $s;
			$years[] = $sy;
			$s++;
			if ($s > 12) {
				$s = 1;
				$sy++;
			}

			if ($s == $e) {
				$months[] = $s;
				$years[] = $sy;
				break;
			}
		}

		$map = function ($event) use ($start, $end, $years, $months) {
			// Fix the year on recurring events
			if ($event['recurring']) {
				$d = new DateTime($event['start_date']);
				$m = $d->format('n');
				$index = array_search($m, $months);

				if ($index !== false) {
					$event['start_date'] = sprintf("%s-%s-%s", $years[$index], $d->format('m'), $d->format('d'));
				}
			}

			return new DateTime($event['start_date']);
		};

		$event = CalendarEvent::whereDate('start_date', '>=', $start)
		                      ->whereDate('start_date', '<=', $end)
		                      ->where('recurring', '=', false)
		                      ->get()->map($map)->toArray();
		$recurring = CalendarEvent::whereRaw(sprintf('MONTH(start_date) IN (%s)', implode(',', $months)))
		                          ->where('recurring', '=', true)
		                          ->get()->map($map)->toArray();

		return array_merge($event, $recurring);
	}	

}
