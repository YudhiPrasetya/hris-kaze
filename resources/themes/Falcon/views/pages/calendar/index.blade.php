@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/calendar.js') }}" defer></script>
@endsection

@section('content')
	<x-bootstrap::row class="small-gutters">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|7">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="row no-gutters align-items-center">
						<x-bootstrap::column breakpoint="AUTO" class="d-flex justify-content-end order-md-1">
							<button type="button" class="btn icon-item icon-item-sm icon-item-hover shadow-none p-0 mr-1" data-event="prev"><i class="fad fa-arrow-left"></i></button>
							<button type="button" class="btn icon-item icon-item-sm icon-item-hover shadow-none p-0 mr-1" data-event="next"><i class="fad fa-arrow-right"></i></button>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|auto;MEDIUM|auto" class="order-md-2">
							<h4 class="mb-0 fs-0 fs-sm-1 fs-lg-2 text-nunito font-weight-semi-bold calendar-title">{{ date('F Y') }}</h4>
						</x-bootstrap::column>
						<x-bootstrap::column class="col d-flex justify-content-center order-md-3 text-center">
							<h4 class="fs-3 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0">
								Events
							</h4>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|auto;MEDIUM|auto" class="d-flex justify-content-end order-md-4">
							{{--
							<div class="dropdown text-sans-serif mr-2">
								<button class="btn btn-falcon-primary text-600 btn-sm dropdown-toggle dropdown-caret-none" id="dropdownMenuButton" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown button</button>
								<div class="dropdown-menu dropdown-menu-right py-0" aria-labelledby="dropdownMenuButton">
									<a class="dropdown-item" href="#">Action</a>
									<a class="dropdown-item" href="#">Another action</a>
									<a class="dropdown-item" href="#">Something else here</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="#">Separated link</a>
								</div>
							</div>
							--}}
							<button class="btn btn-falcon-primary btn-sm mr-2" type="button" data-event="today"><i class="fad fa-calendar-day mr-2"></i>Today</button>
							<a class="btn btn-falcon-primary btn-sm" href="{{ route('calendar.create') }}" {{--data-toggle="modal" data-target="#addEvent"--}}><i class="fad fa-calendar-plus mr-2"></i>Add Event</a>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="p-0 overflow-hidden">
					<div id="calendar"></div>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
		<x-bootstrap::column breakpoint="EXTRA_SMALL|5">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="row no-gutters align-items-center">
						<x-bootstrap::column>
							<h4 class="mb-0 fs-0 fs-sm-1 fs-lg-2 text-nunito font-weight-semi-bold text-red">
								<span class="fad fa-glass-cheers"></span>
								<span>Holidays in {{ date('Y') }}</span>
							</h4>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light p-0 overflow-hidden">
					<div class="list-group list-group-flush font-weight-normal fs--1">
						<div class="list-group-title border-bottom py-1 px-3 bg-200 text-nunito font-weight-semi-bold fs-0">April</div>
						<x-bootstrap::row class="no-gutters">
							<x-bootstrap::column>
								<a class="list-group-item notification notification-flush bg-100 d-flex justify-content-start align-items-start border-t-0 border-x-0" href="#!">
									<div class="notification-avatar">
										<div class="avatar avatar-xl mr-3 rounded-circle border bg-300 d-flex align-items-center justify-content-center">
											<h6 class="mb-0 text-nunito font-weight-semi-bold text-red">13</h6>
										</div>
									</div>
									<div class="notification-body">
										<p class="mb-1">Hari kebangkitan nasional</p>
									</div>
								</a>
								<a class="list-group-item notification notification-flush bg-100 d-flex justify-content-start align-items-start border-t-0 border-x-0" href="#!">
									<div class="notification-avatar">
										<div class="avatar avatar-xl mr-3 rounded-circle border bg-300 d-flex align-items-center justify-content-center">
											<h6 class="mb-0 text-nunito font-weight-semi-bold text-red">13</h6>
										</div>
									</div>
									<div class="notification-body">
										<p class="mb-1">Hari kebangkitan nasional</p>
									</div>
								</a>
							</x-bootstrap::column>
							<x-bootstrap::column>
								<a class="list-group-item notification notification-flush bg-100 d-flex justify-content-start align-items-start border-t-0 border-x-0" href="#!">
									<div class="notification-avatar">
										<div class="avatar avatar-xl mr-3 rounded-circle border bg-300 d-flex align-items-center justify-content-center">
											<h6 class="mb-0 text-nunito font-weight-semi-bold text-red">13</h6>
										</div>
									</div>
									<div class="notification-body">
										<p class="mb-1">Hari kebangkitan nasional</p>
									</div>
								</a>
								<a class="list-group-item notification notification-flush bg-100 d-flex justify-content-start align-items-start border-t-0 border-x-0" href="#!">
									<div class="notification-avatar">
										<div class="avatar avatar-xl mr-3 rounded-circle border bg-300 d-flex align-items-center justify-content-center">
											<h6 class="mb-0 text-nunito font-weight-semi-bold text-red">13</h6>
										</div>
									</div>
									<div class="notification-body">
										<p class="mb-1">Hari kebangkitan nasional</p>
									</div>
								</a>
							</x-bootstrap::column>
						</x-bootstrap::row>
						<div class="list-group-title border-bottom py-1 px-3 bg-200 text-nunito font-weight-semi-bold fs-0">December</div>
						<x-bootstrap::row class="no-gutters">
							<x-bootstrap::column>
								<a class="list-group-item notification notification-flush bg-100 d-flex justify-content-start align-items-start border-t-0 border-x-0" href="#!">
									<div class="notification-avatar">
										<div class="avatar avatar-xl mr-3 rounded-circle border bg-300 d-flex align-items-center justify-content-center">
											<h6 class="mb-0 text-nunito font-weight-semi-bold text-red">13</h6>
										</div>
									</div>
									<div class="notification-body">
										<p class="mb-1">Hari kebangkitan nasional</p>
									</div>
								</a>
								<a class="list-group-item notification notification-flush bg-100 d-flex justify-content-start align-items-start border-t-0 border-x-0" href="#!">
									<div class="notification-avatar">
										<div class="avatar avatar-xl mr-3 rounded-circle border bg-300 d-flex align-items-center justify-content-center">
											<h6 class="mb-0 text-nunito font-weight-semi-bold text-red">13</h6>
										</div>
									</div>
									<div class="notification-body">
										<p class="mb-1">Hari kebangkitan nasional</p>
									</div>
								</a>
							</x-bootstrap::column>
							<x-bootstrap::column>
								<a class="list-group-item notification notification-flush bg-100 d-flex justify-content-start align-items-start border-t-0 border-x-0" href="#!">
									<div class="notification-avatar">
										<div class="avatar avatar-xl mr-3 rounded-circle border bg-300 d-flex align-items-center justify-content-center">
											<h6 class="mb-0 text-nunito font-weight-semi-bold text-red">13</h6>
										</div>
									</div>
									<div class="notification-body">
										<p class="mb-1">Hari kebangkitan nasional</p>
									</div>
								</a>
							</x-bootstrap::column>
						</x-bootstrap::row>
					</div>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	<div class="modal theme-modal fade" id="eventDetails">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border"></div>
		</div>
	</div>
	<div class="modal theme-modal fade" id="addEvent">
		<div class="modal-dialog">
			<div class="modal-header bg-light d-flex flex-between-center border-bottom-0">
				<h5 class="mb-0 modal-title">Add Holiday</h5><button class="close fs-0 px-card" data-dismiss="modal" aria-label="Close"><span class="fas fa-times"></span></button>
			</div>
			<div class="modal-content border">
			{{--
			<form class="form-validation" id="addEventForm" autocomplete="off">
				<div class="modal-body p-card">
					<div class="form-group"><label class="fs-0" for="eventTitle">Title</label><input class="form-control" id="eventTitle" type="text" name="title" required="required" /></div>
					<div class="form-group"><label class="fs-0" for="eventStartDate">Start Date</label><input class="form-control datetimepicker" id="eventStartDate" type="text" required="required" name="startDate" placeholder="y-m-d h:m" data-options='{"static":"true","enableTime":"true","dateFormat":"Y-m-d H:i"}' /></div>
					<div class="form-group"><label class="fs-0" for="eventEndDate">End Date</label><input class="form-control datetimepicker" id="eventEndDate" type="text" name="endDate" placeholder="y-m-d h:m" data-options='{"static":"true","enableTime":"true","dateFormat":"Y-m-d H:i"}' /></div>
					<div class="custom-control custom-checkbox mb-3"><input class="custom-control-input" id="eventAllDay" type="checkbox" name="allDay" /><label class="custom-control-label" for="eventAllDay">All Day</label></div>
					<div class="form-group"> <label class="fs-0">Schedule Meeting</label>
						<div><a class="btn bg-soft-info text-left text-info" href="#!"><span class="fas fa-video mr-2"></span>Add video conference link</a></div>
					</div>
					<div class="form-group"><label class="fs-0" for="eventDescription">Description</label><textarea class="form-control" rows="3" name="description" id="eventDescription"></textarea></div>
					<div class="form-group"><label class="fs-0" for="eventLabel">Label</label><select class="custom-select" id="eventLabel" name="label">
							<option value="" selected="selected">None</option>
							<option value="primary">Business</option>
							<option value="danger">Important</option>
							<option value="success">Personal</option>
							<option value="warning">Must Attend</option>
						</select>
					</div>
				</div>
				<div class="card-footer d-flex justify-content-end align-items-center bg-light">
					<button class="btn btn-primary btn-sm px-4" type="submit">Save</button>
				</div>
				</form>
				--}}
			</div>
		</div>
	</div>
@endsection