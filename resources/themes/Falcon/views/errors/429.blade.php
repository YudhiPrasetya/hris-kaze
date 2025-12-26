@extends('falcon::errors.base')

@section('content')
	{{--<div class="display-1 text-300 fs-error">{{ $exception->getStatusCode() }}</div>--}}
	<div class="display-1 font-weight-bold text-300 fs-error text-nunito">{{ $exception->getStatusCode() }}</div>
	<p class="lead mt-4 text-800 text-sans-serif font-weight-semi-bold">@empty($message) {{ $httpStatusCode->getDescription($exception->getStatusCode()) }} @else {{ $message }} @endif</p>
	<hr />
	<p class="mb-1">{{ $httpStatusCode->getDescription($exception->getStatusCode()) }}If you think this is a mistake, <a href="mailto:info@omnity.id">contact us</a>.</p>
	<a class="btn btn-primary btn-sm mt-3" href="{{ route('home') }}"><i class="fas fa-home mr-2"></i>Take me home</a>
@endsection