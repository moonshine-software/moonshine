@props([
    'components' => [],
])
<aside {{ $attributes->merge(['class' => 'layout-menu']) }}
       :class="{ '_is-minimized': minimizedMenu, '_is-opened': asideMenuOpen }"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</aside>
