@extends('falcon::errors.base')

@section('content')
	{{--<div class="display-1 text-300 fs-error">{{ $exception->getStatusCode() }}</div>--}}
	<div class="display-1 font-weight-bold text-300 fs-error text-nunito">{{ $exception->getStatusCode() }}</div>
	<p class="lead mt-4 text-800 text-sans-serif font-weight-semi-bold">@empty($message) {{ $httpStatusCode::getMessageForCode($exception->getStatusCode()) }} @else {{ $message }} @endif</p>
	<hr />
	<p>Try refreshing the page, or going back and attempting the action again. If this problem persists, <a href="mailto:info@omnity.id">contact us</a>.</p>
	<a class="btn btn-primary btn-sm mt-3" href="{{ route('home') }}"><span class="fas fa-home mr-2"></span>Take me home</a>
@endsection