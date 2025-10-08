@props([
    'components' => [],
])
<!-- Menu horizontal -->
<div {{ $attributes->merge(['class' => 'layout-menu-horizontal']) }}>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
<!-- END: Menu horizontal -->
