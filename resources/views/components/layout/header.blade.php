@props([
    'components' => [],
    'notifications' => true,
    'locales' => true,
])
<div {{ $attributes->merge(['class' => 'layout-navigation']) }}>
    @section("header-inner")

    @show

    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}

    @includeWhen(
        $notifications && config('moonshine.auth.enable', true),
        'moonshine::layouts.shared.notifications'
    )

    @includeWhen(
        $locales && count(config('moonshine.locales', [])) > 1,
        'moonshine::layouts.shared.locales'
    )
</div>
