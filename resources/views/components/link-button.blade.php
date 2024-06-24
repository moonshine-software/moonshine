@props([
    'icon' => false,
    'badge' => false,
    'filled' => false,
])
<a {{ $attributes->class(['btn', 'btn-primary' => $filled]) }}>
    @if($icon)
        <x-moonshine::icon
            :icon="$icon"
            size="4"
        />
    @endif

    {{ $slot }}

    @if($badge !== false)
        <x-moonshine::badge color="">{{ $badge }}</x-moonshine::badge>
    @endif
</a>
