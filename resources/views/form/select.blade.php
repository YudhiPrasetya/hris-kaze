@if($showLabel && $showField)
	@if ($options['wrapper'] !== false)
		<div {!! $options['wrapperAttrs'] !!}>
	@endif
@endif
@if ($showLabel && $options['label'] !== false && $options['label_show'])
	{!! App\Facades\Form::customLabel($name, $options['label'], $options['label_attr']) !!}
@endif
@if ($showField)
	@php
		$emptyVal = $options['empty_value'] ? ['' => $options['empty_value']] : null;
	@endphp
	{!! App\Facades\Form::select($name, (array)$emptyVal + $options['choices'], $options['selected'], $options['attr']) !!}
	@include('laravel-form-builder::help_block')
@endif
@include('laravel-form-builder::errors')
@if($showLabel && $showField)
	@if ($options['wrapper'] !== false)
		</div>
	@endif
@endif
