@props([
    'icon' => false,
    'filled' => false
])
<a {{ $attributes->class(['btn', 'btn-primary' => $filled]) }}>
    <x-moonshine::icon
        :icon="$icon"
        size="4"
    />

    {{ $slot }}
</a>
