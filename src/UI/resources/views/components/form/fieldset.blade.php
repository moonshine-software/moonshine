@props([
    'escapeLabel' => null,
    'label' => '',
    'labelHtml' => null,
])
<fieldset {{ $attributes }}>
    <legend><x-moonshine::display-text :value="$labelHtml ?? $label" :escape="$escapeLabel" /></legend>

    {{ $slot }}
</fieldset>
