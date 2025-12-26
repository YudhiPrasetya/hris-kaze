<nav class="navbar navbar-vertical navbar-expand-xl navbar-light navbar-inverted">
	<div class="d-flex align-items-center">
		<div class="toggle-icon-wrapper">
			<button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-toggle="tooltip" data-placement="left" title="" data-original-title="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
		</div>
		<a class="navbar-brand" href="{{ route('home') }}">
			<div class="d-flex align-items-center py-3">
				<img class="mr-2" src="{{ asset('images/logo/omnity.png') }}" alt="" width="37">
				<div class="flex-column text-left">
					<span class="text-nunito mb-0 font-weight-bold" style="font-size: 1.72rem; color: #0183D0; text-shadow:1px 1px 2px rgba(0,0,0,0.15)">{{ strtoupper(config('app.short_name')) }}</span>
				</div>
			</div>
		</a>
	</div>
	<div class="collapse navbar-collapse" id="navbarVerticalCollapse">
		<ul class="navbar-nav flex-column">
			@include('falcon::extensions.menu.bootstrap-navbar-items', ['items' => $SidebarMenu->roots()])
			<div class="navbar-vertical-divider">
				<hr class="navbar-vertical-hr my-2">
			</div>
			<li class="nav-item mb-2">
				<a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
					<div class="d-flex align-items-center">
						<span class="nav-link-icon"><i class="fad fa-sign-out-alt"></i></span>
						<span class="nav-link-text">{{ __('Logout') }}</span>
					</div>
				</a>
				<form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					@csrf
				</form>
			</li>
		</ul>
	</div>
</nav>
