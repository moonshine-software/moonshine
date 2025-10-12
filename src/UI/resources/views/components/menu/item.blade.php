@props([
    'label' => '',
    'icon' => '',
    'url' => '#',
    'items' => [],
    'isActive' => false,
    'top' => false,
    'button',
])
<li
    {{ $attributes->class(['menu-item', '_is-active' => $isActive]) }}
>
    {!! $button !!}
</li>
