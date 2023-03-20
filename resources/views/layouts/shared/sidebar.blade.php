<!-- Menu -->
<aside class="layout-menu" :class="minimizedMenu && '_is-minimized'">
    <div class="menu-heading">
        <div class="menu-heading-logo">
            @include('moonshine::layouts.shared.logo')
        </div>

        <div class="menu-heading-actions">
            <div class="menu-heading-mode">
                @include('moonshine::layouts.shared.theme-switcher')
            </div>

            <div class="menu-heading-burger">
                <button @click.prevent="asideMenuOpen = ! asideMenuOpen" class="text-white hover:text-pink">
                    <svg x-show="!asideMenuOpen" style="display: none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="asideMenuOpen" style="display: none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @section("sidebar-inner")
    @show

    <nav class="menu" :class="asideMenuOpen && '_is-opened'">
        <!-- Main menu -->
        <x-moonshine::menu-component />

        @includeWhen(
            config('moonshine.auth.enable', true),
            'moonshine::layouts.shared.profile'
        )

        <!-- Bottom menu -->
        <div class="border-t border-dark-200">
            <ul class="menu-inner mt-2">
                <li class="menu-inner-item hidden xl:block">
                    <button @click.prevent="minimizedMenu = ! minimizedMenu" class="menu-inner-button outline-none">
                        <svg x-show="!minimizedMenu" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg x-show="minimizedMenu" style="display: none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="menu-inner-text" x-show="!minimizedMenu">
                            @lang('moonshine::ui.collapse_menu')
                        </span>
                    </button>
                </li>
            </ul>
        </div>
    </nav>
</aside>
<!-- END: Menu -->
