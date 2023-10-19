@props([
    'icon' => false,
    'filled' => false,
])
<a x-data="queryTag()" {{ $attributes->class(['btn', 'btn-primary' => $filled]) }}>
    @if($icon)
        <x-moonshine::icon
            :icon="$icon"
            size="4"
        />
    @endif

    {{ $slot }}
</a>
