@props([
    'label' => '',
    'centered' => false
])
@if($label)
    <div {{ $attributes->class(['divider', 'divider-centered' => $centered]) }}>
        {{ $label }}
    </div>
@else
    <hr {{ $attributes->class(['divider']) }} />
@endif
