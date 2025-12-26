<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Omnity Platform">
	<meta name="author" content="Omnity">
	<meta name="keyword" content="Admin,Dashboard,Credit,Debit,QR,Code,Payment,Digital,Secure">
	{{--<meta name="turbolinks-visit-control" content="reload">--}}
	{{--<meta name="turbolinks-root" content="/">--}}

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

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
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	<link href="{{ themes('css/vendor.css') }}" rel="stylesheet">
	<link href="{{ themes('css/phpdebugbar.css') }}" rel="stylesheet">
	<link href="{{ themes('css/app.css') }}" rel="stylesheet">
	@livewireStyles
	@section('styles')
	@show
</head>
<body>
@yield('content')
<!-- Scripts -->
{{--<script src="{{ asset('js/turbolinks.js') }}"></script>--}}
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('js/manifest.js') }}"></script>
<script src="{{ themes('js/vendor.js') }}"></script>
<script src="{{ asset('js/fontawesome.js') }}" data-auto-replace-svg="true" data-keep-original-source="false" data-search-pseudo-elements="false" data-auto-add-css="true" data-family-prefix="fa"></script>
@livewireScripts
<!-- Optional JavaScript -->
@section('javascripts')
@show
</body>
</html>
