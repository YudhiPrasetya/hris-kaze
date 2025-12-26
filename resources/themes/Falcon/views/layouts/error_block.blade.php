@isset($errors)
	@if ($errors->any())
		@foreach ($errors->all() as $error)
			<div class="alert alert-danger fade show py-2 px-3" role="alert">
				<button class="close" type="button" data-dismiss="alert" aria-label="Close"><span class="font-weight-light" aria-hidden="true">×</span></button>
				<div class="flex-row d-flex align-items-baseline">
					<span class="fad fa-exclamation-triangle mr-2 fs-0 position-relative" style="top: 4px"></span>
					<p class="mb-0">{!! $error !!}</p>
				</div>
			</div>
		@endforeach
	@endif
@endisset