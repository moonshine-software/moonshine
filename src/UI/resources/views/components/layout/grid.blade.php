@props([
    'components' => [],
    'gap' => 6,
])

<div {{ $attributes->merge([
    'class' => Arr::toCssClasses([
        'grid grid-cols-12',
        "gap-$gap",
        "compact:gap-" . round($gap - $gap * 0.2),
        "minimalistic:gap-" . round($gap / 2),
    ]),
]) }}>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
