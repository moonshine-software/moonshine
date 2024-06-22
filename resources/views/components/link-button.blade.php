@props([
    'icon' => null,
    'filled' => false,
    'badge' => false,
])
<a {{ $attributes->class(['btn', 'btn-primary' => $filled]) }}>
    {{ $icon ?? '' }}
    {{ $slot ?? '' }}
    @if($badge !== false)
        <x-moonshine::badge color="">{{ $badge }}</x-moonshine::badge>
    @endif
</a>
