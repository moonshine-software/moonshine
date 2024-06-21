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
        <span class="badge">{{ $badge }}</span>
    @endif
</a>
