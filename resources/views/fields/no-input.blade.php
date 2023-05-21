<div
    @if($element->attributes()->get('x-model-field'))
        x-html="{{ $element->attributes()->get('x-model-field') }}"
    @endif
>
    {!! (string) $element->formViewValue($item) ?? '' !!}
</div>
