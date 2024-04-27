@props([
    'components' => [],
])
<div {{ $attributes->merge(['class' => 'layout-navigation']) }}>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
