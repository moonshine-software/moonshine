@props([
    'icon' => false,
    'filled' => false
])
<a {{ $attributes->class(['inline-flex items-center gap-1 text-2xs hover:text-purple', 'text-purple' => $filled]) }}>
    <x-moonshine::icon
        :icon="$icon"
        size="4"
    />

    {{ $slot }}
</a>
