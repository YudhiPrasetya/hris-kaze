@extends('falcon::layouts.base')

@section('content')
	<x-bootstrap::row>
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|12;LARGE|9">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								{{ $model->name }}<br />
								<small class="fs-0 text-muted d-block">Customer</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							<a href="{{ route('customer.edit', ['customer' => $model->id]) }}" class="btn-falcon-success btn mr-1" role="button"><i class="fad fa-pencil-alt mr-2"></i>Edit</a>
							<button class="btn-falcon-danger btn" type="button" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $model->id }}').submit();">
								<i class="fad fa-trash-alt mr-2"></i>Delete
							</button>
							<form id="delete-form-{{ $model->id }}" action="{{ route('customer.destroy', ['customer' => $model->id]) }}" method="POST" style="display: none;">
								<input type="hidden" name="_method" value="DELETE">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</form>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row>
						<x-bootstrap::column class="overflow-hidden">
							<div class="fancy-tab">
								<div class="nav-bar">
									<div class="nav-bar-item px-3 px-sm-4 active">Info</div>
									<div class="nav-bar-item px-3 px-sm-4">Machines</div>
									<div class="nav-bar-item px-3 px-sm-4">Service Report</div>
								</div>
								<div class="fancy-tab-contents mt-3">
									<div class="tab-content fancy-tab-content active">
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Name</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->name }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Email</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{!! $model->mailto() !!}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Address</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->address() }}</span>
										</div>
									</div>
									<div class="fancy-tab-content" style="margin-top: -1rem">
										@include('themes::Falcon.views.layouts.table', [
											'method' => 'get',
											'canAdd' => true,
											'addUrl' => route('customer.machine.create', ['customer' => $model->id]),
											'addText' => 'Add',
											'hasActions' => true,
											'hasToolbar' => true,
											'data' => [
												'url' => route('api.customer.machine', ['customer' => $model->id]),
												'page-size' => 25,
												'show-refresh' => 'true',
												'method' => 'get',
											],
											'columns' => [
												[
													'title' => 'Serial Number',
													'attrs' => [
														'field' => 'serial_number',
														'class' => 'va-baseline',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Name',
													'attrs' => [
														'field' => 'machine.name',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Type',
													'attrs' => [
														'field' => 'machine.type',
														'class' => 'va-baseline',
														'sortable' => 'true',
													],
												],
											],
										])
									</div>
									<div class="fancy-tab-content" style="margin-top: -1rem">
										@include('themes::Falcon.views.layouts.table', [
											'method' => 'get',
											'canAdd' => true,
											'addUrl' => null,
											'hasActions' => true,
											'hasToolbar' => true,
											'data' => [
												'url' => route('api.assignment.customer', ['customer' => $model->id]),
												'page-size' => 25,
												'show-refresh' => 'true',
												'method' => 'get',
											],
											'columns' => [
												[
													'title' => 'Service Date',
													'attrs' => [
														'field' => 'service_date',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Service Report No.',
													'attrs' => [
														'field' => 'service_no',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'P.O.',
													'attrs' => [
														'field' => 'purchase_order_no',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Chargeable',
													'attrs' => [
														'field' => 'is_chargeable',
														'class' => 'va-baseline text-center',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Product Code',
													'attrs' => [
														'field' => 'product_code',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Machine',
													'attrs' => [
														'field' => 'machine',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Machine Serial Number',
													'attrs' => [
														'field' => 'customer_machine.serial_number',
														'class' => 'va-baseline text-nowrap',
														'sortable' => 'true',
													],
												],
												[
													'title' => 'Status',
													'attrs' => [
														'field' => 'current_status.reason',
														'class' => 'va-baseline',
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
		@include('falcon::pages.customer.info')
	</x-bootstrap::row>
@endsection
