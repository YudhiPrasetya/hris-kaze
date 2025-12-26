<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalendarEventFormRequest;
use App\Http\ViewModels\CalendarEventViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\CalendarEvent;
use App\Repositories\Eloquent\CalendarEventRepository;
use Illuminate\Http\Request;


class CalendarEventController extends Controller {
	private CalendarEventViewModel $viewModel;

	public function __construct(CalendarEventRepository $repository, FormBuilder $builder) {
		$this->viewModel = new CalendarEventViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return $this->viewModel->view('pages.settings.calendar.index');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(CalendarEventFormRequest $request) {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('settings.calendar.index'));
		}

		return $this->create();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
		return $this->viewModel->createForm('POST', 'settings.calendar.store', new CalendarEvent())
		                       ->view('pages.settings.calendar.form');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\CalendarEvent $calendarEvent
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(CalendarEvent $calendar) {
		return redirect(route('calendar.edit', ['calendar' => $calendar->id]));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\CalendarEvent $calendarEvent
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(CalendarEvent $calendar) {
		return $this->viewModel->createForm('PUT', 'settings.calendar.update', $calendar)
		                       ->view('pages.settings.calendar.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request  $request
	 * @param \App\Models\CalendarEvent $calendarEvent
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(CalendarEventFormRequest $request, CalendarEvent $calendar) {
		if (!$this->viewModel->update($request, $calendar)) {
			return redirect(route('settings.calendar.edit', ['calendar' => $calendar->id]));
		}

		return redirect(route('settings.calendar.index'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\CalendarEvent $calendarEvent
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(CalendarEventFormRequest $request, CalendarEvent $calendar) {
		if (!$this->viewModel->delete($request, $calendar)) {
			return redirect(route('settings.calendar.edit', ['calendar' => $calendar->id]));
		}

		return redirect(route('settings.calendar.index'));
	}

	public function nationalEvents(Request $request) {
		return $this->viewModel->nationalEvents($request);
	}
}
