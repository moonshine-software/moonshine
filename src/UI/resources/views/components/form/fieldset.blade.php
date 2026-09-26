@props([
    'escapeLabel' => null,
    'label' => '',
])
<fieldset {{ $attributes }}>
    <legend><x-moonshine::display-text :value="$label" :escape="$escapeLabel" /></legend>

    {{ $slot }}
</fieldset>
