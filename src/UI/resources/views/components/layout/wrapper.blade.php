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

    <div
        class="layout-overlay"
        x-cloak
        x-show="asideMenuOpen"
        x-on:click="asideMenuOpen = false"
        x-transition.opacity=""
    >
    </div>
</div>
