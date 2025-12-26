<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- ===============================================-->
	<!--    Document Title-->
	<!-- ===============================================-->
	<title>{{ lang('messages.name') }}</title>

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
	<link href="{{ themes('css/vendor.css') }}" rel="stylesheet">
	<link href="{{ themes('css/phpdebugbar.css') }}" rel="stylesheet">
	<link href="{{ themes('css/app.css') }}" rel="stylesheet">
	@livewireStyles
	@section('styles')
	@show
</head>
<body>
<main class="main" id="top">
	<div class="container-fluid" data-layout="container">
		{{--<div class="row flex-center min-vh-100 py-6 text-center">--}}
		<div class="row flex-center min-vh-100 text-center">
			<div class="col-sm-10 col-md-8 col-lg-6 col-xxl-5">
				<a class="d-flex flex-center mb-4 text-decoration-none" href="{{ route('home') }}">
					<img class="mr-2" src="{{ asset('images/logo/omnity.png') }}" alt="" width="58" />
					<span class="text-nunito font-weight-extra-bold fs-5 d-inline-block">{{ strtoupper(lang('messages.short_name')) }}</span>
				</a>
				<div class="card">
					<div class="card-body p-4 p-sm-5">
						@yield('content')
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('js/manifest.js') }}"></script>
<script src="{{ themes('js/vendor.js') }}"></script>
<script src="{{ asset('js/fontawesome.js') }}" data-auto-replace-svg="true" data-keep-original-source="false" data-search-pseudo-elements="false" data-auto-add-css="true" data-family-prefix="fa"></script>
<!-- Scripts -->
<script src="{{ themes('js/app.js') }}" defer></script>
@livewireScripts
@section('javascripts')
@show
</body>
</html>