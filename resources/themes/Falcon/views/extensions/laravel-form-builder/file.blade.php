@if ($showLabel && $showField)
	@if ($options['wrapper'] !== false)
		<div {!! $options['wrapperAttrs'] !!}>
	@endif
@endif
@if ($showLabel && $options['label'] !== false && $options['label_show'])
	{!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
@endif
@if ($showField)
	<div class="custom-file">
		{!! Form::file($name, $options['attr']) !!}
		<label class="custom-file-label text-truncate pr-7" for="{{ $name }}">Choose file</label>
	</div>
	@include('laravel-form-builder::help_block')
@endif
@include('laravel-form-builder::errors')
@if ($showLabel && $showField)
	@if ($options['wrapper'] !== false)
		</div>
	@endif
@endif
