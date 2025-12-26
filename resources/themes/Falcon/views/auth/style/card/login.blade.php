<main class="main" id="top">
	<div class="container-fluid">
		<div class="row min-vh-100 flex-center no-gutters">
			<div class="col-lg-8 col-xxl-5 py-3">
				{{--<img class="bg-auth-circle-shape" src="{{ themes('images/illustrations/bg-shape.png') }}" alt="" width="250">--}}
				<img class="bg-auth-circle-shape" src="{{ themes('images/bg-shape.png') }}" alt="" width="250">
				<img class="bg-auth-circle-shape-2" src="{{ themes('images/illustrations/shape-1.png') }}" alt="" width="150">
				<div class="card overflow-hidden z-index-1">
					<div class="card-body p-0">
						<div class="row no-gutters h-100">
							<div class="col-md-5 text-white text-center bg-card-gradient">
								<div class="position-relative p-4 pt-md-5 pb-md-7">
									{{--<div class="bg-holder bg-auth-card-shape" style="background-image:url({{ themes('images/illustrations/half-circle.png') }});"></div>--}}
									{{--<div class="bg-holder bg-auth-card-shape" style="background-image:url({{ themes('images/half-circle.png') }});"></div>--}}
									<!--/.bg-holder-->
									<div class="z-index-1 position-relative">
										<div class="d-flex flex-row align-items-center justify-content-center mb-4">
											<img src="{{ asset('images/logo/omnity.png') }}" width="52">
											<a class="text-white ml-2 text-nunito font-weight-bold text-uppercase fs-4 d-inline-block text-decoration-none" href="{{ route('home') }}">{{ config('app.short_name') }}</a>
										</div>
										<p class="text-white opacity-75">{!! lang('messages.login.note', ['product_name' => config('app.short_name')]) !!}</p>
									</div>
								</div>
								<div class="mt-3 mb-4 mt-md-4 mb-md-5">
									{{--<p>@lang("Don't have an account?")<br><a class="text-white text-underline" href="{{ route('register') }}">Get started!</a></p>--}}
									<p class="mb-0 mt-4 mt-md-5 fs--1 font-weight-semi-bold text-white opacity-75">{!! lang('messages.login.terms_and_conditions', ['terms' => '!#', 'conditions' => '!#']) !!}</p>
								</div>
							</div>
							<div class="col-md-7 d-flex flex-center">
								<div class="p-4 p-md-5 flex-grow-1">
									<h3 class="text-nunito font-weight-semi-bold">@lang('Account Login')</h3>
									@if (session('warning'))
										<div class="alert alert-warning fade show fs--1 px-3 py-2" role="alert">
											<div class="row p-0">
												<div class="col-1 px-2 text-center"><i class="fad fa-exclamation-triangle"></i></div>
												<div class="col-10 p-0"><span>{{ session('warning') }}</span></div>
												<div class="col-1 px-2 text-center"><button class="close fs-1 float-none" type="button" data-dismiss="alert" aria-label="Close"><span class="font-weight-light" aria-hidden="true">×</span></button></div>
											</div>
										</div>
									@endif
									@if (session('danger'))
										<div class="alert alert-danger fade show fs--1 px-3 py-2" role="alert">
											<div class="row p-0">
												<div class="col-1 px-2 text-center"><i class="fad fa-exclamation-triangle"></i></div>
												<div class="col-10 p-0"><span>{{ session('danger') }}</span></div>
												<div class="col-1 px-2 text-center"><button class="close fs-1 float-none" type="button" data-dismiss="alert" aria-label="Close"><span class="font-weight-light" aria-hidden="true">×</span></button></div>
											</div>
										</div>
									@endif
									<form method="POST" action="{{ route('login') }}">
										{!! csrf_field() !!}
										<div class="form-group">
											<label for="card-email">@lang('E-Mail Address')</label>
											{{--<input id="card-email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>--}}
											<input id="card-email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="" required autocomplete="email" autofocus>
											@error('email')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
											@enderror
										</div>
										<div class="form-group">
											<div class="d-flex justify-content-between">
												<label for="card-password">@lang('Password')</label>
												<a class="fs--1" href="{{ route('password.request') }}">@lang('Forgot Your Password?')</a>
											</div>
											<input id="card-password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="" required autocomplete="current-password">
											@error('password')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
											@enderror
										</div>
										<div class="custom-control custom-checkbox">
											<input class="custom-control-input" type="checkbox" id="card-checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
											<label class="custom-control-label" for="card-checkbox">@lang('Remember Me')</label>
										</div>
										<div class="form-group">
											<button class="btn btn-primary btn-block mt-3" type="submit" name="submit">@lang('Login')</button>
										</div>
									</form>
									<div class="col-12 col-sm-auto text-center">
										<p class="mb-0 text-600 fs--1">{{ Str::concat(' - ', Str::title(env('APP_ENV')), env('APP_VERSION_NAME'), env('APP_VERSION')) }}</p>
									</div>
									{{--
									<div class="w-100 position-relative mt-4">
										<hr class="text-300" />
										<div class="position-absolute absolute-centered t-0 px-3 bg-white text-sans-serif fs--1 text-500 text-nowrap">or log in with</div>
									</div>
									<div class="form-group mb-0">
										<div class="row no-gutters">
											<div class="col-sm-6 pr-sm-1">
												<a class="btn btn-outline-google-plus btn-sm btn-block mt-2" href="#">
													<i class="fab fa-google-plus-g mr-2" data-fa-transform="grow-8"></i> google
												</a>
											</div>
											<div class="col-sm-6 pl-sm-1">
												<a class="btn btn-outline-facebook btn-sm btn-block mt-2" href="#">
													<i class="fab fa-facebook-square mr-2" data-fa-transform="grow-8"></i> facebook
												</a>
											</div>
										</div>
									</div>
									--}}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
