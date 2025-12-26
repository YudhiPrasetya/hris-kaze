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
										<p class="text-white opacity-75">{!! lang('messages.login.note', ['product_name' => config('app.short_name')]) !!}</p>
									</div>
								</div>
								<div class="mt-3 mb-4 mt-md-4 mb-md-5">
									<p class="mb-0 mt-4 mt-md-5 fs--1 font-weight-semi-bold text-white opacity-75">Read our <a class="text-underline text-white" href="#!">terms</a> and <a class="text-underline text-white" href="#!">conditions </a></p>
								</div>
							</div>
							<div class="col-md-7 d-flex flex-center">
								<div class="p-4 p-md-5 flex-grow-1">
									<div class="text-center text-md-left">
										<h4 class="mb-0">Forgot your password?</h4>
										<p class="mb-4">Enter your email and we'll send you a reset link.</p>
									</div>
									<div class="row justify-content-center">
										<div class="col-sm-8 col-md">
											<form method="POST" action="{{ route('password.email') }}">
												<div class="form-group"><input class="form-control" type="email" placeholder="Email address"></div>
												<div class="form-group"><button class="btn btn-primary btn-block mt-3" type="submit" name="submit">Send reset link</button></div>
											</form>
											{{--<a class="fs--1 text-600" href="#!">I can't recover my account using this page<span class="d-inline-block ml-1">→</span></a>--}}
											<a class="fs--1 text-600" href="{{ route('login') }}">Take me login page<i class="fal fa-arrow-right ml-2"></i></a>
										</div>
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