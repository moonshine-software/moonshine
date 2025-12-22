@props([
    'components' => [],
    'gap' => 6,
])

<div {{ $attributes->merge([
    'class' => "grid grid-cols-12 gap-$gap",
]) }}>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
