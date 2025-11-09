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
        x-show="$store.menu.isSidebarOpen"
        x-on:click="$store.menu.toggleSidebar()"
        x-transition.opacity
    >
    </div>
</div>
