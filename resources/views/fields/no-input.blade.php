<div
    @if($element->attributes()->get('x-model-field'))
        x-html="{{ $element->attributes()->get('x-model-field') }}"
    @endif
>
    {!! $element->value() ?? '' !!}
</div>
