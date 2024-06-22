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
    {{ $attributes->class(['menu-inner-item', '_is-active' => $isActive]) }}
>
    {!! $button !!}
</li>
