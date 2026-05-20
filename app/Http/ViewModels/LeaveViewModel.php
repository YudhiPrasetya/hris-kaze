<?php

namespace App\Http\ViewModels;

use App\Http\Forms\LeaveForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use App\Models\Attendance;
use App\Models\CalendarEvent;
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
		// $this->form->setUrl(route($route, ['leave' => $model->id]));
		$this->form->setUrl(route($route, ['employee' => $model->id]));

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

    public function cancelLeave(Request $request){
        $leaveId = (int)$request->leave;
        $employeeId = (int)$request->employee;
        $start = new \DateTime($request->start);
        $end = new \DateTime($request->end);

        if(Leave::find($leaveId)->forceDelete()){
            while($start <= $end){
                $att = Attendance::where('employee_id', '=', $employeeId)->where('at', '=', $start);
                if($att){
                    $att->forceDelete();
                }
                $start->modify('+ 1 day');
            }
            $request->session()->flash('message', "Successfully cancel <strong>Leave</strong>.");
            $request->session()->flash('alert', "success");
            // return redirect(route('permit.index'));
        }else{
            $request->session()->flash('message', "Failed to cancel permit with id <strong>{$leaveId}</strong>");
            $request->session()->flash('alert', "danger");
        }
        redirect('/leave')->send();
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

                    $action = [
                        'cancelLeave' => [
                        'icon' => 'fad fa-ban',
                        'attr' => [
                            'class' => 'btn btn-sm btn-falcon-warning',
                            'target' => '_self',
                            'href' => route('leave.cancel', [
                                'leave' => $result['id'],
                                'employee' => $result['id_employee'],
                                'start' => $result['start'],
                                'end' =>  $result['end']
                                // 'params' => $params
                            ]),
                        ],
                        'type' => 'a',
                        'tooltip' => 'Cancel leave'
                    ]];

					// return $self->addDefaultListActions($result, 'edit', 'destroy');

                    $result['actions'] = $action;
                    return $result;

					// return $self->addDefaultListActions($result, 'edit', 'destroy');
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
		$employeeId = $fields->get('id_employee');
		$startDate = new \DateTime($fields->get('start'));
		$endDate = new \DateTime($fields->get('end'));
		$months = [$startDate->format('n'), $endDate->format('n')];

		$eventsCalendar = CalendarEvent::where('start_date', '>=', $startDate)
			->where('start_date', '<=', $endDate)
			->where('recurring', false)
			->get()->pluck('start_date');

		$recurringEvents = CalendarEvent::whereRaw(sprintf('MONTH(start_date) IN (%s)', implode(',', $months)))
			->where('recurring', true)
			->get()->pluck('start_date')->map(function ($date) use ($startDate, $endDate) {
				$eventDate = new \DateTime($date);
				$eventDate->setDate($startDate->format('Y'), $eventDate->format('m'), $eventDate->format('d'));

				if ($eventDate >= $startDate && $eventDate <= $endDate) {
					return $eventDate->format('Y-m-d');
				}

				return null;
			})->filter();

		$eventsCalendar = $eventsCalendar->merge($recurringEvents);

		if ($fields->has('attachment_path'))
			$fields->offsetSet('attachment_path', $this->convertImage($request, 'attachment_path'));

		$fields->offsetSet('cut_att_premium', $this->toBool($fields->get('cut_att_premium')));
		$p = $fields->toArray();
		$leave = new Leave($p);
		$ret = $leave->save();

		// Add leave attendaces
		$dateStartLeave = new \DateTime($fields->get('start'));
		$dateEndLeave = new \DateTime($fields->get('end'));
		// $eventCalendar = new \DateTime($eventsCalendar['start_date']);
		while($dateStartLeave <= $dateEndLeave){
			$dayOfWeek = $dateStartLeave->format('w');
			$checkNotInEventCalendar = in_array($dateStartLeave->format('Y-m-d'), $eventsCalendar->toArray());
			if($checkNotInEventCalendar === false && ($dayOfWeek != "0" && $dayOfWeek != "6")){
				$attLeave = new Attendance([
					'employee_id' => $employeeId,
					'attendance_reason_id' => 6,
					'at' => $dateStartLeave
				]);

				$attLeave->save();
			}
			$dateStartLeave->modify('+1 day');
		}


		// for($x = 0; $x < $leaveDays; $x++){
		// 	$date = $dateLeave->modify('+'.$x.' day');
		// 	dump($date);
		// 	// $dayOfWeek = $date->format('w');
		// 	// if($date != $eventCalendar && ($dayOfWeek != "0" && $dayOfWeek != "6")){
		// 	// 	$attLeave = new Attendance([
		// 	// 		'employee_id' => $employeeId,
		// 	// 		'attendance_reason_id' => 6,
		// 	// 		'at' => $date
		// 	// 	]);

		// 	// 	$attLeave->save();
		// 	// }
		// 	// $dayIndex = date('w', strtotime($dateLeave->modify('+'.$x.' day')->format('Y-m-d H:i:s')));

		// 	// if($dateLeave->modify('+'.$x.' day') !== new \DateTime($eventCalendar['start_date']) && ($dayIndex !== "0" || $dayIndex !== "6")){
		// 	// 	$attLeave = new Attendance([
		// 	// 		'employee_id' => $employeeId,
		// 	// 		'attendance_reason_id' => 6,
		// 	// 		'at' => $dateLeave->modify('+'.$x.' day')
		// 	// 	]);


		// 	// 	$attLeave->save();
		// 	// }

		// }
		return $ret;
	}

}
