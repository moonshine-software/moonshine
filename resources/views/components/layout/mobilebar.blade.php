@props([
    'components' => [],
    'home_route' => null,
    'hideLogo' => false,
    'hideSwitcher' => false,
    'logo',
    'profile',
])
<aside {{ $attributes->merge(['class' => 'layout-menu-mobile']) }}
       :class="minimizedMenu && '_is-minimized'"
>
    <div class="menu-heading">
        @if(!$hideLogo)
            <div class="menu-heading-logo">
                @if($logo ?? false)
                    {{ $logo }}
                @else
                    @include('moonshine::layouts.shared.logo', ['home_route' => $home_route])
                @endif
            </div>
        @endif

        <div class="menu-heading-actions">
            @if(!$hideSwitcher && config('moonshine.use_theme_switcher', true))
                <div class="menu-heading-mode">
                    <x-moonshine::layout.theme-switcher :top="false" />
                </div>
            @endif

            <div class="menu-heading-burger">
                @include('moonshine::layouts.shared.burger')
            </div>
        </div>
    </div>

    <nav class="menu" :class="asideMenuOpen && '_is-opened'">
        <x-moonshine::components
            :components="$components"
        />

        {{ $slot ?? '' }}
    </nav>
</aside>
