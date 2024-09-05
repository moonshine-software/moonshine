@props([
    'components' => [],
])
<aside {{ $attributes->merge(['class' => 'layout-menu-mobile']) }}
       :class="minimizedMenu && '_is-minimized'"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</aside>
