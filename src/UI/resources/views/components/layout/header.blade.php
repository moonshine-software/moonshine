@props([
    'components' => [],
    'translates' => [],
])
<div {{ $attributes->merge(['class' => 'layout-navigation']) }}>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
