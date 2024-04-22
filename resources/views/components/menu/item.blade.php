@props([
    'label' => '',
    'icon' => '',
    'badge' => '',
    'url' => '#',
    'items' => [],
    'isActive' => false,
    'top' => false,
    'actionButton',
])
<li
    {{ $attributes->class(['menu-inner-item', '_is-active' => $isActive]) }}
>
    {!! $actionButton !!}
</li>
