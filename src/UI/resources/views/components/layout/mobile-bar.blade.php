@props([
    'components' => [],
])
<!-- Mobile bar -->
<div {{ $attributes->merge(['class' => 'layout-menu-mobile layout-menu-horizontal']) }}
    :class="$store.menu.isMobileBarOpen && '_is-opened'"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
<!-- END: Mobile bar -->
