@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
    <div {!! $options['wrapperAttrs'] !!}>
    @endif
@endif
@if ($showField)
    <div class="custom-control custom-switch">
        {!! Form::checkbox($name, $options['value'], $options['checked'], $options['attr']) !!}
        @if ($options['label_show'])
            {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
        @endif
    </div>
	@include('laravel-form-builder::help_block')
@endif
@include('laravel-form-builder::errors')
@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif
