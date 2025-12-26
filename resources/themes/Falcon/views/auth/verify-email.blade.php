@extends('falcon::layouts.base_minimal')

@section('content')
<main class="main" id="top">
	<div class="container-fluid">
		<div class="row min-vh-100 flex-center no-gutters">
			<div class="col-lg-8 col-xxl-5 py-3">
				<img class="bg-auth-circle-shape" src="{{ themes('images/bg-shape.png') }}" alt="" width="250">
				<img class="bg-auth-circle-shape-2" src="{{ themes('images/illustrations/shape-1.png') }}" alt="" width="150">
				<div class="card overflow-hidden z-index-1">
					<div class="card-body p-0">
						<div class="row no-gutters h-100">
							<div class="col-md-5 text-white text-center bg-card-gradient">
								<div class="position-relative p-4 pt-md-5 pb-md-7">
									<div class="bg-holder bg-auth-card-shape" style="background-image:url({{ themes("images/half-circle.png") }});"></div>
									<!--/.bg-holder-->
									<div class="z-index-1 position-relative">
										<div class="d-flex flex-row align-items-center justify-content-center mb-4">
											<img src="{{ asset('images/logo/omnity.png') }}" width="52">
											<a class="text-white ml-2 text-nunito font-weight-bold text-uppercase fs-4 d-inline-block text-decoration-none" href="{{ route('home') }}">{{ config('app.short_name') }}</a>
										</div>
										<p class="text-white opacity-75">{{ lang('messages.login.note', ['product_name' => config('app.short_name')]) }}</p>
									</div>
								</div>
								<div class="mt-3 mb-4 mt-md-4 mb-md-5">
									<p class="mb-0 mt-4 mt-md-5 fs--1 font-weight-semi-bold text-white opacity-75">{!! lang('messages.login.terms_and_conditions', ['terms' => '!#', 'conditions' => '!#']) !!}</p>
								</div>
							</div>
							<div class="col-md-7 d-flex flex-center">
								<div class="p-4 p-md-5 flex-grow-1">
									<div class="text-center">
										<img class="d-block mx-auto mb-4" src="{{ themes('images/illustrations/envelope.png') }}" alt="Email" width="70">
										<h3 class="mb-2 text-nunito font-weight-semi-bold">Please check your email!</h3>
										<p>{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
										@if (session('status') == 'verification-link-sent')
											<div class="mb-4 font-medium text-sm text-green-600">
												{{ __('A new verification link has been sent to the email address you provided during registration.') }}
											</div>
										@endif
										<div class="mt-4 d-flex justify-content-between align-items-center">
											<form method="POST" action="{{ route('verification.send') }}">
												@csrf
												<button type="submit" class="btn btn-primary btn-sm mt-3" href="{{ route('login') }}">
													{{ __('Resend Verification Email') }}
												</button>
											</form>

											<form method="POST" action="{{ route('logout') }}">
												@csrf

												<button type="submit" class="btn btn-sm btn-link underline text-sm text-gray-600 hover:text-gray-900">
													{{ __('Logout') }}
												</button>
											</form>
										</div>
										{{--
										<a class="btn btn-primary btn-sm mt-3" href="{{ route('login') }}">
											<i class="fas fa-chevron-left mr-1" data-fa-transform="shrink-4 down-1"></i>Return to login
										</a>
										--}}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection
{{--
<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-jet-button type="submit">
                        {{ __('Resend Verification Email') }}
                    </x-jet-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>
--}}
