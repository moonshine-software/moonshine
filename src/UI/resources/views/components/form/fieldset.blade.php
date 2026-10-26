@props([
    'label' => '',
    'labelHtml' => null,
])
<fieldset {{ $attributes }}>
    <legend>{{ $labelHtml ?? $label }}</legend>

    {{ $slot }}
</fieldset>
