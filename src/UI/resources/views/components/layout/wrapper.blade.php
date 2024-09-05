@props([
    'components' => []
])
<div {{ $attributes->merge(['class' => 'layout-wrapper']) }}
     :class="minimizedMenu && 'layout-wrapper-short'"
     x-data="interactsWithAsync"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
