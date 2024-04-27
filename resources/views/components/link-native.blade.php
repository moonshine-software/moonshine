@props([
    'icon' => false,
    'filled' => false
])
<a {{ $attributes->class(['inline-flex items-center gap-1 text-2xs hover:text-primary', 'text-primary' => $filled]) }}>
    @if($icon)
        <x-moonshine::icon
            :icon="$icon"
            size="4"
        />
    @endif

    {{ $slot ?? '' }}
</a>
