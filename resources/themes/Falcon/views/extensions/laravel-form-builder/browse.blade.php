@if ($showLabel && $showField)
	@if ($options['wrapper'] !== false)
		<div {!! $options['wrapperAttrs'] !!}>
	@endif
@endif
@if ($showLabel && $options['label'] !== false && $options['label_show'])
	{!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
@endif
@if ($showField)
	<div {!! $options['btnAttr'] !!}>
		@if ($options['btnLabel'])
		<span>{!! $options['btnLabel'] !!}</span>
		@endif
		{!! Form::file($name, $options['attr']) !!}
	</div>
	@include('laravel-form-builder::help_block')
@endif
@include('laravel-form-builder::errors')
@if ($showLabel && $showField)
	@if ($options['wrapper'] !== false)
		</div>
	@endif
@endif
