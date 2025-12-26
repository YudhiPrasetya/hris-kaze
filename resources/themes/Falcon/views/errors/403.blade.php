@extends('falcon::errors.base')

@section('content')
	{{--<div class="display-1 text-300 fs-error">{{ $exception->getStatusCode() }}</div>--}}
	<div class="display-1 font-weight-bold text-300 fs-error text-nunito">{{ $exception->getStatusCode() }}</div>
	<p class="lead mt-4 text-800 text-sans-serif font-weight-semi-bold">{{ $httpStatusCode->getDescription($exception->getStatusCode()) }}</p>
	<hr />
	<p>{{ $message ?? '' }}</p>
	@if($isLoggedIn)
		<a class="btn btn-primary btn-sm mt-3" href="{{ route('home') }}"><i class="fas fa-home mr-2"></i>Go to to home page</a>
	@else
		<a class="btn btn-primary btn-sm mt-3" href="{{ route('login') }}"><i class="fas fa-sign-in-alt mr-2"></i>Go to to login page</a>
	@endif
@endsection