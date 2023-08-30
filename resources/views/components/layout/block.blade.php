@props([
    'components' => []
])
<div {{ $attributes }}>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
