@props([
    'icon' => null,
    'filled' => false,
])
<a {{ $attributes->class(['btn', 'btn-primary' => $filled]) }}>
    {{ $icon ?? '' }}
    {{ $slot ?? '' }}
</a>
