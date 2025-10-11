@props([
    'label',
])
<li {{ $attributes->class('menu-divider') }}>
    {!! $label? "<span>$label</span>" : '' !!}
</li>
