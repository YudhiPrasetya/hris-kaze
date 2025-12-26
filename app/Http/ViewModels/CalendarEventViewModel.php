<?php

namespace App\Http\ViewModels;

use App\Http\Forms\CalendarEventForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\CalendarEvent;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class CalendarEventViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'calendar';
		$this->routeKey = 'calendar';
		$this->form = $this->formBuilder->create(CalendarEventForm::class);
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['calendar' => $model->id]));

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
		if (!CalendarEvent::find($model->id)->forceDelete()) {
			$request->session()->flash('message', "Failed to delete <strong>{$model->title}</strong>");
			$request->session()->flash('alert', "danger");
		}
		else {
			$request->session()->flash('message', "Successfully delete <strong>{$model->title}</strong>.");
			$request->session()->flash('alert', "success");
		}

		return redirect(route('calendar.index'));
	}

	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$fields->offsetSet('end_date', $fields['start_date']);
		$calendar = new CalendarEvent($fields->toArray());
		$ret = $calendar->save();

		return $ret ? $calendar : false;
	}

	public function nationalEvents(Request $request) {
		list($offset, $limit, $sort, $order, $search, $date, $start, $end) = $this->getDefaultRequestParam($request);
		// $current = $end->sub(new \DateInterval('P1M'));
		// print_r(CalendarEvent::whereMonth('start_date', '=', $current->format('n'))->where('recurring', '=', true)->toSql());
		$months = [];
		$years = [];
		$s = (int) $start->format('n');
		$sy = (int) $start->format('Y');
		$e = (int) $end->format('n');

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

		$map = function ($event) use($start, $end, $years, $months) {
			$event['allDay'] = true;

			// Fix the year on recurring events
			if ($event['recurring']) {
				$d = new \DateTime($event['start_date']);
				$m = $d->format('n');
				$index = array_search($m, $months);

				if ($index !== false) {
					$event['start_date'] = sprintf("%s-%s-%s", $years[$index], $d->format('m'), $d->format('d'));
				}
			}

			$event['start'] = $event['start_date'];
			$event['textColor'] = '#FFFFFF';
			$event['backgroundColor'] = '#E63757';
			$event['token'] = csrf_token();
			unset($event['start_date']);

			return $event;
		};

		$event = CalendarEvent::whereDate('start_date', '>=', $start)
		                      ->whereDate('start_date', '<=', $end)
		                      ->where('recurring', '=', false)
		                      ->get()->map($map);
		$recurring = CalendarEvent::whereRaw(sprintf('MONTH(start_date) IN (%s)', implode(',', $months)))
		                          ->where('recurring', '=', true);

		return $event->merge($recurring->get()->map($map));
	}
}
