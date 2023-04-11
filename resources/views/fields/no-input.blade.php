<div
    @if($element->attributes()->get('x-model'))
        x-text="{{ $element->attributes()->get('x-model') }}"
    @endif
>
    {!! (string) $element->formViewValue($item) ?? '' !!}
</div>
