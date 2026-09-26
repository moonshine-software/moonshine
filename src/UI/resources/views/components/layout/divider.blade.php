@props([
    'escapeLabel' => null,
    'label' => '',
    'centered' => false
])
@if($label)
    <div {{ $attributes->class(['divider', 'divider-centered' => $centered]) }}>
        <x-moonshine::display-text :value="$label" :escape="$escapeLabel" />
    </div>
@else
    <hr {{ $attributes->class(['divider']) }} />
@endif
