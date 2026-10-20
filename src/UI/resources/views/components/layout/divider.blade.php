@props([
    'escapeLabel' => null,
    'label' => '',
    'labelHtml' => null,
    'centered' => false
])
@if($label)
    <div {{ $attributes->class(['divider', 'divider-centered' => $centered]) }}>
        <x-moonshine::display-text :value="$labelHtml ?? $label" :escape="$escapeLabel" />
    </div>
@else
    <hr {{ $attributes->class(['divider']) }} />
@endif
