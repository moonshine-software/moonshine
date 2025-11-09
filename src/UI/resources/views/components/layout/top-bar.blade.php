@props([
    'components' => [],
])
<!-- Top bar -->
<div {{ $attributes->merge(['class' => 'layout-menu-horizontal']) }}
    :class="$store.menu.isTopbarOpen && '_is-opened'"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
<!-- END: Top bar -->
