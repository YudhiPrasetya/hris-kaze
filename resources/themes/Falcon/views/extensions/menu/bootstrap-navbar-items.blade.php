@foreach($items as $item)
	@if($item->divider)
		<div class="navbar-vertical-divider" {!! App\Managers\Menu\Builder::attributes($item->divider) !!}>
			<hr class="navbar-vertical-hr my-2">
		</div>
	@else
		@php
			$icon = null;
			$turboLinks = null;

			if (isset($item->attributes['icon'])) {
				$icon = $item->attributes['icon'];
				unset($item->attributes['icon']);
			}

			if (isset($item->attributes['data-turbolinks'])) {
				$turboLinks = $item->attributes['data-turbolinks'];
				unset($item->attributes['data-turbolinks']);
			}

			if (!isset($parent)) {
				$parent = 'navbarVerticalCollapse';
			}
		@endphp
		<li @lm_attrs($item) @if($item->hasChildren()) class="nav-item dropdown" @else class="nav-item" @endif @lm_endattrs>
			@if($item->link)
				@if($item->hasChildren())
					<a @lm_attrs($item->link) class="nav-link dropdown-indicator" href="#{{ $item->id }}" @if(!is_null($turboLinks)) data-turbolinks="{{ $turboLinks }}" @endif data-toggle="collapse" role="button" aria-expanded="false" aria-controls="home" @lm_endattrs>
						<div class="d-flex align-items-center">
							@if($icon)
								<span class="nav-link-icon"><i class="{{ $icon }}"></i></span>
							@endif
							<span class="nav-link-text">{!! $item->title !!}</span>
						</div>
					</a>
				@else
					<a class="nav-link" href="{!! $item->url() !!}" @if(!is_null($turboLinks)) data-turbolinks="{{ $turboLinks }}" @endif>
						<div class="d-flex align-items-center">
							@if($icon)
								<span class="nav-link-icon"><i class="{{ $icon }}"></i></span>
							@endif
							<span class="nav-link-text">{!! $item->title !!}</span>
						</div>
					</a>
				@endif
			@else
				<span class="navbar-text">{!! $item->title !!}</span>
			@endif
			@if($item->hasChildren())
				<ul class="nav collapse {{ $item->isActive ? 'show' : null }}" id="{{ $item->id }}" data-parent="#{{ $parent }}">
					@include('falcon::extensions.menu.bootstrap-navbar-items', array('items' => $item->children(), 'isChildren' => true, 'parent' => $item->id))
				</ul>
			@endif
		</li>
	@endif
@endforeach
