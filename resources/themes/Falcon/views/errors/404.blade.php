@extends('falcon::errors.base')

@section('content')
	{{--<div class="display-1 text-300 fs-error">{{ $exception->getStatusCode() }}</div>--}}
	<div class="display-1 font-weight-bold text-300 fs-error text-nunito">{{ $exception->getStatusCode() }}</div>
	<p class="lead mt-4 mb-0 text-800 text-sans-serif font-weight-semi-bold">@empty($message) The page you're looking for is not found. @else {{ $message }} @endif</p>
	<p class="text-800 text-sans-serif mt-0">{{ $httpStatusCode->getDescription($exception->getStatusCode()) }}</p>
	<hr />
	<p>Make sure the address is correct and that the page hasn't moved. If you think this is a mistake, <a href="mailto:info@omnity.id">contact us</a>.</p>
	<a class="btn btn-primary btn-sm mt-3" href="{{ route('home') }}"><i class="fad fa-home mr-2"></i>Take me home</a>
@endsection