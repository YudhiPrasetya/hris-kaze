
@extends('falcon::layouts.base')
@section('content')
	<x-bootstrap::row>
		<x-bootstrap::column>
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								{{ $model->name }}<br />
								<small class="fs-0 text-muted d-block">Employee</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							<a href="{{ route('employee.edit', ['employee' => $model->id]) }}" class="btn-falcon-success btn mr-1" role="button"><i class="fad fa-pencil-alt mr-2"></i>Edit</a>
							<button class="btn-falcon-danger btn" type="button" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $model->id }}').submit();">
								<i class="fad fa-trash-alt mr-2"></i>Delete
							</button>
							<form id="delete-form-{{ $model->id }}" action="{{ route('employee.destroy', ['employee' => $model->id]) }}" method="POST" style="display: none;">
								<input type="hidden" name="_method" value="DELETE">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</form>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|2" class="d-flex flex-column align-items-center justify-content-center">
							<div class="avatar w-90 mb-3">
								<figure class="figure w-100">
									<img id="targetPreview" src="@if(!$model->profile_photo_path->isEmpty()){!! $model->profile_photo_path->toString() !!}@else{{ route('image-placeholder', ['size' => '512', 'bgColor' => '#EEF0F2', 'textColor' => '#ffffff']) }}@endif" class="figure-img img-fluid rounded-circle img-thumbnail w-100 transition-all" alt="Profile Picture" />
								</figure>
							</div>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|10" class="">
							<div class="fancy-tab">
								<div class="nav-bar">
									<div class="nav-bar-item px-3 px-sm-4 active">Info</div>
									<div class="nav-bar-item px-3 px-sm-4">Assignments</div>
									<div class="nav-bar-item px-3 px-sm-4">Tasks</div>
									<div class="nav-bar-item px-3 px-sm-4">Attendance</div>
								</div>

								<div class="fancy-tab-contents mt-3">
									<div class="tab-content fancy-tab-content active">
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Name</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->name }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">NIK</h6>
											<span id="nik" class="form-control-plaintext text-1000 fs-0">{{ $model->nik }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Job Title</h6>
											<span id="position" class="form-control-plaintext text-1000 fs-0">{{ $model->jobTitle()->first()->name ?? "-"}}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Position</h6>
											<span id="jobtitle" class="form-control-plaintext text-1000 fs-0">{{ $model->position()->first()->name ?? "-"}}</span>
										</div>
										@php
											$year = (new \DateTime())->diff($model->effective_since)->y;
											$month = (new \DateTime())->diff($model->effective_since)->m;
											$day = (new \DateTime())->diff($model->effective_since)->d;
										@endphp
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Effective Since</h6>
											<span id="effective_since" class="form-control-plaintext text-1000 fs-0">{{ $model->effective_since->format('l, d F Y') }} &mdash; {{ $year }} years, {{ $month }} months, {{ $day }} days</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Leave Allowance</h6>
											<span id="leaveAllowance" class="form-control-plaintext text-1000 fs-0">{{ $model->leave_allowance == null ? '0' : $model->leave_allowance}}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">User</h6>
											<span id="user" class="form-control-plaintext text-1000 fs-0">{!! $model->user()?->link() !!}</span>
										</div>
										<x-bootstrap::row>
											<x-bootstrap::column breakpoint="MEDIUM|6">
												<x-bootstrap::media variant="primary" class="my-4" icon="fad fa-money-check-alt" title="Salary" subtitle="Employee salary." />
												<x-bootstrap::column class="px-0">
													<x-bootstrap::media variant="primary" class="mt-4" title="<small>Earnings</small>" />
												</x-bootstrap::column>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Basic Salary</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($model->basic_salary, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Functional Allowance</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($model->functional_allowance, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Transport Allowance</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($model->transport_allowance, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Meal Allowances</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($model->meal_allowances, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Other Allowances</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($model->other_allowance, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">
														Overtime<br />
														<small class="text-600">Total overtimes {{ $payroll->employee->presences->overtimeDays }} day(s)</small>
													</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->earnings->overtime, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Attendance Premium<br /><small>Premi kehadiran</small></label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->earnings->attendance_premium, $model->currencyCode()) !!}</div>
												</div>
												<x-bootstrap::column class="px-0">
													<x-bootstrap::media variant="primary" class="mt-4" title="<small>Deductions</small>" />
												</x-bootstrap::column>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">
														BPJS Kesehatan<br />
														<small class="text-600">Total dependents {{ $payroll->employee->numOfDependentsFamily }}</small>
													</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->deductions->BPJSKesehatan, $model->currencyCode()) !!}</div>
												</div>

												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">JHT <small class="text-600">(Jaminan Hari Tua)</small></label>
													<div class="col-sm-7 col-form-label">
                                                        {!! $moneyFormat($payroll->result->deductions->JHT ?? 0, $model->currencyCode()) !!}
                                                    </div>
												</div>

												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">JIP <small class="text-600">(Jaminan Pensiun)</small></label>
													<div class="col-sm-7 col-form-label">
                                                        {!! $moneyFormat($payroll->result->deductions->JIP ?? 0, $model->currencyCode()) !!}
                                                    </div>
												</div>

												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Pajak Jabatan</label>
													<div class="col-sm-7 col-form-label">
                                                        {!! $moneyFormat($payroll->result->deductions->positionTax ?? 0, $model->currencyCode()) !!}
                                                    </div>
												</div>

												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">PPH 21 <small class="text-600">({{ $payroll->result->taxable->rate }})</small></label>
													<div class="col-sm-7 col-form-label">
                                                        {!! $moneyFormat($payroll->result->deductions->pph21Tax ?? 0, $model->currencyCode()) !!}
                                                    </div>
												</div>

												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">
														Presences<br />
														<small class="text-600">Total {{ $payroll->employee->presences->workDays }} days from {{ $payroll->provisions->company->numOfWorkingDays }} total work days</small>
													</label>
													<div class="col-sm-7 col-form-label">
                                                        {!! $moneyFormat($payroll->result->deductions->presence ?? 0, $model->currencyCode()) !!}
                                                    </div>
												</div>

												<x-bootstrap::column class="px-0">
													<x-bootstrap::media variant="primary" class="mt-4" title="<small>NETT</small>" />
												</x-bootstrap::column>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Annually</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->earnings->annually->nett, $model->currencyCode()) !!}</div>
												</div>
												<x-bootstrap::column class="px-0">
													<x-bootstrap::media variant="primary" class="mt-4" title="<small>Penghasilan Tidak Kena Pajak</small>" />
												</x-bootstrap::column>
												<div class="form-group row mb-0">
													<label class="col-sm-3 col-form-label">Status</label>
													<div class="col-sm-2 col-form-label">{!! $payroll->result->taxable->ptkp->status !!}</div>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->taxable->ptkp->amount, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">PKP</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->taxable->pkp, $model->currencyCode()) !!}</div>
												</div>
												<x-bootstrap::column class="px-0">
													<x-bootstrap::media variant="primary" class="mt-4" title="<small>PPH {{ $payroll->result->taxable->liability->rule }}</small>" />
												</x-bootstrap::column>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Per-bulan</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->taxable->liability->monthly, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Per-tahun</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->taxable->liability->annual, $model->currencyCode()) !!}</div>
												</div>


												<x-bootstrap::column class="px-0">
													<x-bootstrap::media variant="primary" class="mt-4" title="<small>Presences</small>" />
												</x-bootstrap::column>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Present</label>
													<div class="col-sm-7 col-form-label">{{ $payroll->employee->presences->workDays ?? 0 }} day(s)</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Sick</label>
													<div class="col-sm-7 col-form-label">{{ $payroll->employee->presences->sick ?? 0 }} day(s)</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Business Trip</label>
													<div class="col-sm-7 col-form-label">{{ $payroll->employee->presences->business_trip ?? 0 }} day(s)</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Permits</label>
													<div class="col-sm-7 col-form-label">{{ $payroll->employee->presences->permit ?? 0 }} day(s)</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Absents</label>
													<div class="col-sm-7 col-form-label">{{ $payroll->employee->presences->absent ?? 0 }} day(s)</div>
												</div>


												<x-bootstrap::column class="px-0">
													<x-bootstrap::media variant="primary" class="mt-4" title="<small>Total</small>" />
												</x-bootstrap::column>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Total Earnings</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->earnings->baseTotal, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Total Deductions</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->deductions->getSum() - $payroll->result->deductions->positionTax, $model->currencyCode()) !!}</div>
												</div>
												<div class="form-group row mb-0">
													<label class="col-sm-5 col-form-label">Take Home Pay</label>
													<div class="col-sm-7 col-form-label">{!! $moneyFormat($payroll->result->takeHomePay, $model->currencyCode()) !!}</div>
												</div>

                                                <div class="row">
                                                    <button class="btn-falcon-warning btn" type="button" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $model->id }}').submit();">
                                                        <i class="fad fa-print mr-2"></i>Cetak Slip Gaji
                                                    </button>
                                                </div>
											</x-bootstrap::column>
											<x-bootstrap::column>
												<x-bootstrap::media variant="primary" class="my-4" icon="fad fa-money-check-alt" title="AnnualLeave " subtitle="Annual Leave." />
											</x-bootstrap::column>
										</x-bootstrap::row>
									</div>
									<div class="fancy-tab-content">
										@include('themes::Falcon.views.layouts.table', [
											'method' => 'get',
											'canAdd' => true,
											'addUrl' => '#',
											'addText' => 'Add',
											'hasActions' => true,
											'hasToolbar' => true,
											'data' => [
												'url' => route('api.assignment.employee', ['employee' => $model->id]),
												'page-size' => 25,
												'show-refresh' => 'true',
												'method' => 'get',
												'search' => 'true',
											],
											'columns' => [
												[
													'title' => 'Service Report No.',
													'attrs' => [
														'field' => 'assignment.service_no',
														'class' => 'va-baseline',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Date',
													'attrs' => [
														'field' => 'assignment.service_date',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Customer',
													'attrs' => [
														'field' => 'assignment.customer.name',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Machine',
													'attrs' => [
														'field' => 'assignment.customer_machine.machine.name',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Machine Type',
													'attrs' => [
														'field' => 'assignment.customer_machine.machine.type',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Status',
													'attrs' => [
														'field' => 'assignment.current_status.reason',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
											],
										])
									</div>
									<div class="fancy-tab-content">
										@include('themes::Falcon.views.layouts.table', [
											'method' => 'get',
											'hasActions' => true,
											'hasToolbar' => true,
											'data' => [
												'url' => route('api.task.employee', ['employee' => $model->id]),
												'page-size' => 25,
												'show-refresh' => 'true',
												'method' => 'get',
												'search' => 'true',
											],
											'columns' => [
												[
													'title' => '',
													'attrs' => [
														'field' => 'priority.name',
														'class' => 'va-baseline text-nowrap text-center',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Title',
													'attrs' => [
														'field' => 'title',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Dateline',
													'attrs' => [
														'field' => 'dateline',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Assign At',
													'attrs' => [
														'field' => 'at',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Status',
													'attrs' => [
														'field' => 'current_status.reason',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
											],
										])
									</div>
									<div class="fancy-tab-content">
										@include('themes::Falcon.views.layouts.table', [
											'method' => 'get',
											'hasActions' => false,
											'hasToolbar' => true,
											'data' => [
												'url' => route('api.attendance.employee', ['employee' => $model->id]),
												'page-size' => 25,
												'show-refresh' => 'true',
												'method' => 'get',
												'search' => 'false',
												'sort-name' => "at",
												'sort-order' => "desc"
											],
											'columns' => [
												[
													'title' => 'Date',
													'attrs' => [
														'field' => 'at',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Check-In',
													'attrs' => [
														'field' => 'start',
														'class' => 'va-baseline text-center text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Check-Out',
													'attrs' => [
														'field' => 'end',
														'class' => 'va-baseline text-center text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Overtime',
													'attrs' => [
														'field' => 'overtime',
														'class' => 'va-baseline text-center text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Total Hours',
													'attrs' => [
														'field' => 'total_hours',
														'class' => 'va-baseline text-center text-nowrap',
														'sortable' => 'false',
													],
												],
												[
													'title' => 'Total Overtime',
													'attrs' => [
														'field' => 'total_overtime',
														'class' => 'va-baseline text-center text-nowrap',
														'sortable' => 'false',
													],
												],
												[
													'title' => 'Presence',
													'attrs' => [
														'field' => 'reason.name',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Attachment',
													'attrs' => [
														'field' => 'attactment',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
											],
										])
									</div>
								</div>
							</div>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
@endsection
