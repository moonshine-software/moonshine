@props([
    'icon' => false,
    'badge' => false,
    'filled' => false,
])
<a {{ $attributes->class(['inline-flex items-center gap-1 text-2xs hover:text-primary', 'text-primary' => $filled]) }}>
    @if($icon)
        <x-moonshine::icon
            :icon="$icon"
            size="4"
        />
    @endif

    {{ $slot }}

    @if($badge !== false)
        <x-moonshine::badge color="primary">{{ $badge }}</x-moonshine::badge>
    @endif
</a>
