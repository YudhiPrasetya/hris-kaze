<main class="main" id="top">
	<div class="container-fluid" data-layout="container">
		<div class="row flex-center min-vh-100 py-6">
			<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
				<a class="d-flex flex-center mb-4 text-decoration-none" href="{{ route('home') }}">
					<img class="mr-2" src="{{ asset('images/logo/omnity.png') }}" alt="" width="58">
					<span class="text-nunito text-uppercase font-weight-bold fs-5 d-inline-block">{{ config('app.short_name') }}</span>
				</a>
				<div class="card">
					<div class="card-body p-4 p-sm-5">
						<div class="row text-left justify-content-between align-items-center mb-2">
							<div class="col-auto">
								<h5>Log in</h5>
							</div>
							<div class="col-auto">
								<p class="fs--1 text-600 mb-0">or <a href="{{ route('register') }}">Create an account</a>
								</p>
							</div>
						</div>
						<form method="POST" action="{{ route('login') }}">
							<div class="form-group">
								<input class="form-control" type="text" placeholder="Email address">
							</div>
							<div class="form-group">
								<input class="form-control" type="password" placeholder="Password">
							</div>
							<div class="row justify-content-between align-items-center">
								<div class="col-auto">
									<div class="custom-control custom-checkbox">
										<input class="custom-control-input" type="checkbox" id="basic-checkbox" checked="checked"><label class="custom-control-label" for="basic-checkbox">Remember me</label>
									</div>
								</div>
								<div class="col-auto">
									<a class="fs--1" href="{{ route('password.request') }}">Forgot Password?</a>
								</div>
							</div>
							<div class="form-group">
								<button class="btn btn-primary btn-block mt-3" type="submit" name="submit">Log in</button>
							</div>
						</form>
						{{--
						<div class="w-100 position-relative mt-4">
							<hr class="text-300">
							<div class="position-absolute absolute-centered t-0 px-3 bg-white text-sans-serif fs--1 text-500 text-nowrap">or log in with</div>
						</div>
						<div class="form-group mb-0">
							<div class="row no-gutters">
								<div class="col-sm-6 pr-sm-1">
									<a class="btn btn-outline-google-plus btn-sm btn-block mt-2" href="#"><i class="fab fa-google-plus-g mr-2" data-fa-transform="grow-8"></i> google</a>
								</div>
								<div class="col-sm-6 pl-sm-1">
									<a class="btn btn-outline-facebook btn-sm btn-block mt-2" href="#"><span class="fab fa-facebook-square mr-2" data-fa-transform="grow-8"></span> facebook</a>
								</div>
							</div>
						</div>
						--}}
					</div>
				</div>
			</div>
		</div>
	</div>
</main>