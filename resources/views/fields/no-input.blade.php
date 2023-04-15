<div
    @if($element->attributes()->get('x-model-field'))
        x-text="{{ $element->attributes()->get('x-model-field') }}"
    @endif
>
    {!! (string) $element->formViewValue($item) ?? '' !!}
</div>
