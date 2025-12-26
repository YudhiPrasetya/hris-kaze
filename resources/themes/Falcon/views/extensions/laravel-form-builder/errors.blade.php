@if ($showError && isset($errors) && $errors->hasBag($errorBag))
    @foreach ($errors->getBag($errorBag)->get($nameKey) as $err)
        <div {!! $options['errorAttrs'] !!}><span class="fad fa-exclamation-triangle mr-2"></span>{!! $err !!}</div>
    @endforeach
@endif

