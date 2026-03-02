@props([
    'label' => '',
    'labelRaw' => false,
    'escapeLabel' => false,
])
<fieldset {{ $attributes }}>
    <legend>
        @if(! $escapeLabel || $labelRaw)
            {!! $label !!}
        @else
            {{ $label }}
        @endif
    </legend>

    {{ $slot }}
</fieldset>
