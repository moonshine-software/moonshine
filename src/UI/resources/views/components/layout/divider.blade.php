@props([
    'label' => '',
    'labelHtml' => null,
    'centered' => false
])
@if($label)
    <div {{ $attributes->class(['divider', 'divider-centered' => $centered]) }}>
        {{ $labelHtml ?? $label }}
    </div>
@else
    <hr {{ $attributes->class(['divider']) }} />
@endif
