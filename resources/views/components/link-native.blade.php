@props([
    'icon' => null,
    'filled' => false
])
<a {{ $attributes->class(['inline-flex items-center gap-1 text-2xs hover:text-primary', 'text-primary' => $filled]) }}>
    {{ $icon ?? '' }}
    {{ $slot ?? '' }}
</a>
