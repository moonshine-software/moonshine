@aware(['type'])
@props([
    'icon' => false,
    'filled' => false,
])
<a {{ $attributes->class(['btn', 'btn-primary' => $filled, "btn-$type"]) }}>
    @if($icon)
        <x-moonshine::icon
            :icon="$icon"
            size="4"
        />
    @endif

    {{ $slot }}
</a>
