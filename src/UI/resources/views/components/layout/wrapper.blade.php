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
        x-show="$store.menu.sidebarMenuOpen"
        x-on:click="$store.menu.toggleSidebarMenu()"
        x-transition.opacity
    >
    </div>
</div>
