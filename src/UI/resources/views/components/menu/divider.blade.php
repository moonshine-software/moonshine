@props([
    'label',
    'labelHtml' => null,
])
<li {{ $attributes->class('menu-divider') }}>
    @if($label)
        <span>{{ $labelHtml ?? $label }}</span>
    @endif
</li>
