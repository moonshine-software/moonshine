@props([
    'components' => [],
])
<!-- Menu horizontal -->
<aside {{ $attributes->merge(['class' => 'layout-menu-horizontal']) }}
       :class="asideMenuOpen && '_is-opened'"
       x-data="{minimizedMenu: false}"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</aside>
<!-- END: Menu horizontal -->
