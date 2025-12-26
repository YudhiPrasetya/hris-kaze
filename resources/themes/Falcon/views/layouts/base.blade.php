<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Omnity Platform">
	<meta name="author" content="Omnity">
	<meta name="keyword" content="Admin,Dashboard,Credit,Debit,QR,Code,Payment,Digital,Secure">
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
	{{--<meta name="turbolinks-visit-control" content="reload">--}}
	{{--<meta name="turbolinks-root" content="/">--}}

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- ===============================================-->
	<!--    Document Title-->
	<!-- ===============================================-->
	<title>{{ config('app.name') }}</title>

	<!-- ===============================================-->
	<!--    Favicons-->
	<!-- ===============================================-->
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo/omnity-apple-touch.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo/omnity-32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo/omnity-16.png') }}">
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo/omnity.ico') }}">
	<link rel="manifest" href="/images/logo/manifest.json">
	<meta name="msapplication-TileImage" content="{{ asset('images/logo/omnity-mstile-150.png') }}">
	<meta name="theme-color" content="#ffffff">

	<!-- ===============================================-->
	<!--    Stylesheets-->
	<!-- ===============================================-->
	<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

	<link href="{{ themes('css/vendor.css') }}" rel="stylesheet">
	<link href="{{ themes('css/phpdebugbar.css') }}" rel="stylesheet">
	<link href="{{ themes('css/app.css') }}" rel="stylesheet">
	@livewireStyles
	@section('styles')
	@show

	<!-- Scripts -->
	{{--<script src="{{ asset('js/turbolinks.js') }}"></script>--}}
</head>
<body>
<main class="main" id="top">
	<div class="wrapper scrollbar-dynamic">
		<div class="container-fluid">
			@include('falcon::layouts.sidebar')
			<div class="content mb-3 transition-all">
				@include('falcon::layouts.navbar')
				<div class="mt-1">
					{{ \App\Facades\Breadcrumbs::render() }}
					@if(Session::has('message'))
						@php
							$class = (array) Session::get('alert', 'danger');
							$messages = (array) Session::get('message', 'No message available');
						@endphp
						@foreach($messages as $key => $message)
						<div class="alert alert-{{ $class[$key] }} fade show py-2 px-3" role="alert">
							<button class="close" type="button" data-dismiss="alert" aria-label="Close"><span class="font-weight-light" aria-hidden="true">×</span></button>
							<div class="flex-row d-flex align-items-baseline">
								<span class="fad fa-{{ $class == 'danger' ? 'exclamation-circle' : ($class == 'warning' ? 'exclamation-triangle' : 'check-circle') }} mr-2 fs-0 position-relative" style="top: 4px"></span>
								<p class="mb-0">{!! $message !!}</p>
							</div>
						</div>
						@endforeach
					@endif
					@yield('content')
				</div>
			</div>
		</div>
		<footer>
			<div class="row no-gutters justify-content-between mt-4 pb-3">
				<div class="col-12 col-sm-auto text-center">
					@php
						$start = 2021;
						$copyright = '%s © <a href="%s">%s</a>';
						$copyright = sprintf($copyright, 2021 == date('Y') ? date('Y') : '2021 - ' . date('Y'), route('home'), env('APP_NAME'));
					@endphp
					<p class="mb-0 text-600">{!! $copyright !!}</a></p>
				</div>
				<div class="col-12 col-sm-auto text-center">
					<p class="mb-0 text-600">{{ Str::concat(' - ', Str::title(env('APP_ENV')), env('APP_VERSION_NAME'), env('APP_VERSION')) }}</p>
				</div>
			</div>
		</footer>
	</div>
</main>
<div class="toast-container overflow-hidden p-2" style="position: absolute; top: 77px; right: 15px; bottom: 0px"></div>
{{--<script src="https://cdn.jsdelivr.net/gh/underground-works/clockwork-browser@1/dist/toolbar.js"></script>--}}
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('js/manifest.js') }}"></script>
<script src="{{ themes('js/vendor.js') }}"></script>
<script src="{{ asset('js/fontawesome.js') }}" data-auto-replace-svg="true" data-keep-original-source="false" data-search-pseudo-elements="false" data-auto-add-css="true" data-family-prefix="fa"></script>
<script src="{{ asset('js/tinymce/tinymce.js') }}" defer></script>
<script src="{{ themes('js/app.js') }}" defer></script>
@livewireScripts
<!-- Optional JavaScript -->
@section('javascripts')
@show
</body>
</html>
