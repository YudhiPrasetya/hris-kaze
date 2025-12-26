@extends('falcon::layouts.base')

@section('styles')
	@parent
	<link href="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css" rel="stylesheet" />
	<style>
	.overlay {
		position: absolute;
		top: 10px;
		left: 10px;
	}

	.overlay button {
		font: 600 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
		background-color: #3386c0;
		color: #fff;
		display: inline-block;
		margin: 0;
		padding: 10px 20px;
		border: none;
		cursor: pointer;
		border-radius: 3px;
	}

	.overlay button:hover {
		background-color: #4ea0da;
	}
	</style>
@endsection

@section('javascripts')
	@parent
	<script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>
	<script src="{{ asset('js/mapbox-gl.js') }}" defer></script>
	<script src="{{ themes('js/tracker.js') }}" defer></script>
@endsection

@section('content')
	<x-bootstrap::row class="justify-content-center small-gutters">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|3;LARGE|2" class="mb-sm-3 mb-md-0 pr-sm-0 pr-md-3">
			<div class="list-group shadow">
				@foreach($vehicles as $vehicle)
					<a href="#" class="list-group-item list-group-item-action tracker-item" aria-current="true" data-imei="{{ $vehicle->imei }}">
						<div class="d-flex w-100 justify-content-between align-items-baseline">
							<h5 class="mb-1"><span class="badge badge-dark">{{ $vehicle->plat_number }}</span></h5>
							<small id="tracker-{{ $vehicle->imei }}" class="rounded-circle" style="width: 16px; height: 16px">&nbsp;</small>
						</div>
						<small>ID: {{ $vehicle->imei }}</small>
					</a>
				@endforeach
			</div>
		</x-bootstrap::column>
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|9;LARGE|10">
			<x-bootstrap::card class="bg-light overflow-hidden">
				<x-bootstrap::card.body id="map" style="min-height: calc(100vh - 192px) !important"></x-bootstrap::card.body>
				{{--<div class="overlay">--}}
				{{--	<button id="replay">Replay</button>--}}
				{{--</div>--}}
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
@endsection