@props([
    'label',
])
<li {{ $attributes->class('menu-inner-divider') }}>
    {!! $label? "<span>$label</span>" : '' !!}
</li>
