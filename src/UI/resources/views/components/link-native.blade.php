@props([
    'icon' => null,
    'filled' => false,
    'badge' => false,
])
<a {{ $attributes->class(['inline-flex items-center gap-1 max-w-full', 'text-primary' => $filled]) }}>
    {{ $icon ?? '' }}
    {{ $slot ?? '' }}
    @if($badge !== false)
        <x-moonshine::badge color="">{{ $badge }}</x-moonshine::badge>
    @endif
</a>
