@props([
    'label' => '',
    'labelRaw' => false,
])
<fieldset {{ $attributes }}>
    <legend>
        @if($labelRaw)
            {!! $label !!}
        @else
            {{ $label }}
        @endif
    </legend>

    {{ $slot }}
</fieldset>
