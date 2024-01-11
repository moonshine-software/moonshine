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

    @if($locales)
        <x-moonshine::layout.locales />
    @endif
</div>
