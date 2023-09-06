@props([
    'components' => []
])
<div {{ $attributes->merge(['class' => 'layout-wrapper']) }}
     :class="minimizedMenu && 'layout-wrapper-short'"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
