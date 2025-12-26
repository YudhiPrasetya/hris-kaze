@extends('falcon::layouts.base_minimal')

@section('content')
	@isset($loginType)
		@include('falcon::auth.style.card.login')
	@else
		@include('falcon::auth.style.card.login')
	@endisset
@endsection
