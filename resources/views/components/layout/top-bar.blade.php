@props([
    'components' => []
])
<!-- Menu horizontal -->
<aside {{ $attributes->merge(['class' => 'layout-menu-horizontal']) }}
       :class="asideMenuOpen && '_is-opened'"
>
    <div class="menu-logo">
        @include('moonshine::layouts.shared.logo')
    </div>

    <nav class="menu-navigation">
        <x-moonshine::components
            :components="$components"
        />

        {{ $slot ?? '' }}
    </nav>

    <div class="menu-actions">
        @if(config('moonshine.auth.enable', true))
            <x-moonshine::layout.profile />
        @endif

        <div class="menu-inner-divider"></div>

        <div class="menu-mode">
            <x-moonshine::layout.theme-switcher :top="true" />
        </div>

        <div class="menu-burger">
            @include('moonshine::layouts.shared.burger')
        </div>
    </div>
</aside>
<!-- END: Menu horizontal -->

