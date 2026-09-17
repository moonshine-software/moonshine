@props([
    'escapeLabel' => moonshine()->getConfig()->isEscapeLabel(),
    'label' => '',
])
<fieldset {{ $attributes }}>
    <legend>{!! $escapeLabel ? e($label) : $label !!}</legend>

    {{ $slot }}
</fieldset>
