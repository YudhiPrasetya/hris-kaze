@if($showLabel && $showField)
	@if ($options['wrapper'] !== false)
		<div {!! $options['wrapperAttrs'] !!}>
	@endif
@endif
@if ($showLabel && $options['label'] !== false && $options['label_show'])
	{!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
@endif
@if ($showField)
	<div class="input-group">
		@if ($showPrepend)
			<div class="input-group-prepend"><span class="input-group-text">{!! $options['prepend'] !!}</span></div>
		@endif
		{!! Form::input($type, $name, $options['value'], $options['attr']) !!}
		@if ($showAppend)
				<div class="input-group-append"><span class="input-group-text">{!! $options['append'] !!}</span></div>
		@endif
	</div>
	@include('laravel-form-builder::help_block')
@endif
@include('laravel-form-builder::errors')
@if($showLabel && $showField)
	@if ($options['wrapper'] !== false)
		</div>
	@endif
@endif
