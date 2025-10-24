@props([
    'label' => '',
    'icon' => '',
    'url' => 'javascript:void(0);',
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
