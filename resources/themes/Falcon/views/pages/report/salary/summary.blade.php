@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/default-select.js') }}" defer></script>
	<script src="{{ asset('js/date.js') }}" defer></script>
	<script>
		function cleanCurrency(value) {
			if (!value) return 0;
			let parser = new DOMParser();
			let doc = parser.parseFromString(value, 'text/html');
			let text = doc.body.textContent || "";
			// Menghapus "Rp", titik sebagai pemisah ribuan, dan mengganti koma dengan titik
			return parseFloat(text.replace(/[^0-9,-]+/g, '').replace(/\./g, '').replace(',', '.'));
		}

		function formatCurrency(num) {
			// Konversi ke string dengan dua tempat desimal
			let str = num.toFixed(2);
			// Pisahkan bagian desimal dan bagian integer
			let parts = str.split('.');
			// Format bagian integer dengan titik sebagai pemisah ribuan
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
			// Gabungkan bagian integer dan desimal dengan koma sebagai pemisah desimal
			return 'Rp ' + parts.join(',');
		}


		function sumFormatterPPhMonthly(data) {
			let sum = data.map(row => {
				let value = row['payroll'] ? cleanCurrency(row['payroll']['deductions']['pph21_monthly']) : 0;
				return isNaN(value) ? 0 : value;
			}).reduce((sum, i) => sum + i, 0);
			return formatCurrency(sum); // Optional: format to 2 decimal places
		}

		function sumFormatterTakeHomePay(data) {
			let sum = data.map(row => {
				let value = row['payroll'] ? cleanCurrency(row['payroll']['total']['take_home_pay']) : 0;
				return isNaN(value) ? 0 : value;
			}).reduce((sum, i) => sum + i, 0);
			return formatCurrency(sum); // Optional: format to 2 decimal places
		}
	</script>
@endsection

@section('content')
	@php
		$fields = collect($form->getFieldValues());
	@endphp
	{!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
	<x-bootstrap::row class="justify-content-center">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|5">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								Salary Report
								<small class="fs-0 text-muted d-block">List of all salary</small>
							</h5>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					{{-- <div class="form-group row">
						{!! form_label($form->employee, ['label_attr' => ['class' => 'col-sm-3 col-form-label']]) !!}
						<div class="col-sm-9">
							{!! form_widget($form->employee, ['attr' => ['class_append' => 'select2', 'data-value' => $request->input('employee')]]) !!}
						</div>
					</div> --}}
					<div class="form-group row">
						{!! form_label($form->month, ['label_attr' => ['class' => 'col-sm-3 col-form-label']]) !!}
						<div class="col-sm-6">
							{!! form_widget($form->month, ['attr' => ['class_append' => 'select2', 'data-value' => $request->input('month', date('m'))]]) !!}
						</div>
						<div class="col-sm-3">
							{!! form_widget($form->year, ['value' => $request->input('year', date('Y'))]) !!}
						</div>
					</div>
					<div class="form-group d-flex flex-column">
						{!! form_row($form->submit) !!}
					</div>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	{!! form_end($form, $renderRest = true) !!}
	@if($request?->isMethod('POST'))
	<x-bootstrap::row class="justify-content-center mt-4">
		<x-bootstrap::column>
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								Result&nbsp;&mdash;&nbsp;{!! sprintf("%s &dash; %s", $data['start']->format('F'), $data['end']->format('F Y')) !!}
								<small class="fs-0 text-muted d-block">List of all salary</small>
							</h5>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light overflow-hidden p-0">
					<div id="toolbar">
						<label class="col-form-label font-weight-normal">For Periode: 26 {{$data['start']->format('M')}} - 25 {{$data['end']->format('M')}}</label>
					</div>
					<table
						class="bootstrap-table-free"
						data-method="get"
						data-pagination="true"
						data-page-size="25"
						data-show-refresh="true"
						data-sort-name="no"
						data-sort-order="asc"
						data-url="{{ route('api.salary.report', ['start' => sprintf("%s-%s-01", $request->input('year'), $request->input('month'))]) }}"
						data-show-footer="true"
						data-toolbar="#toolbar"
						data-row-style="rowStyle">
						<thead class="thead-light">
						<tr>
							<th rowspan="2" scope="col" data-class="text-center va-middle font-weight-medium" data-field="no" data-sortable="false" width="50">#</th>
							<th rowspan="2" scope="col" data-class="va-middle text-nowrap" data-field="name" data-sortable="false">Name</th>
							<th rowspan="2" scope="col" data-class="va-middle text-nowrap" data-field="payroll.earnings.base" data-sortable="false">Basic Salary</th>
							<th colspan="4" scope="col" data-class="text-center va-baseline" data-sortable="false">Allowances</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="attendances" data-sortable="false">Attendance</th>
							<th colspan="2" scope="col" data-class="text-center va-baseline" data-sortable="false">Overtime</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle" data-field="thr" data-sortable="false" data-width="35">THR</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.total.earning" data-sortable="false" data-width="35">Total Earnings</th>
							<th colspan="3" scope="col" data-class="text-center va-baseline" data-sortable="false">Company</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.total.bruto_monthly" data-sortable="false" data-width="35">Bruto Monthly</th>
							<th colspan="2" scope="col" data-class="text-center va-baseline" data-sortable="false">Employee</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.deductions.position_tax" data-sortable="false" data-width="35">Position Fee</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.total.nett_annual" data-sortable="false" data-width="35">Nett Annually</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.ptkp.amount" data-sortable="false" data-width="35">PTKP</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.ptkp.pkp" data-sortable="false" data-width="35">PKP</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.deductions.pph21_annual" data-sortable="false" data-width="35">PPH 21 Annually</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.deductions.pph21_monthly" data-sortable="false" data-width="35" data-footer-formatter="sumFormatterPPhMonthly">PPH 21 Monthly</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.total.take_home_pay" data-sortable="false" data-footer-formatter="sumFormatterTakeHomePay">Take Home Pay</th>
						</tr>
						<tr>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.earnings.allowance.functional" data-sortable="false">Functional</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.earnings.allowance.transport" data-sortable="false">Transport</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.earnings.allowance.meal" data-sortable="false">Meal</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.earnings.allowance.other" data-sortable="false">Other</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="outbound" data-sortable="false">Outbound</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="inbound" data-sortable="false">Inbound</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.deductions.bpjs_kesehatan" data-sortable="false">BPJS Kesehatan</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.deductions.jkk" data-sortable="false">JKK</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.deductions.jkm" data-sortable="false">JKM</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.deductions.jip" data-sortable="false">JP</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="payroll.deductions.jht" data-sortable="false">JHT</th>
						</tr>
						</thead>
					</table>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	@endif
@endsection
