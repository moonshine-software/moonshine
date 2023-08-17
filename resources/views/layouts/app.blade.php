<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>
    <head>
        @include('moonshine::layouts.shared.head')
        @moonShineAssets
    </head>
    <body x-cloak
          x-data="{ minimizedMenu: $persist(false).as('minimizedMenu'), asideMenuOpen: false }"
    >
        <div class="layout-wrapper"
             :class="minimizedMenu && 'layout-wrapper-short'"
        >
            @section('sidebar')
                @include(config('moonshine.templates.sidebar', 'moonshine::layouts.shared.sidebar'))
            @show

            <div class="layout-page">
                @include('moonshine::layouts.shared.flash')

                @section('header')
                    @include(config('moonshine.templates.header', 'moonshine::layouts.shared.header'))
                @show

                <main class="layout-content">
                    @yield('content')
                </main>

                @section('footer')
                    @include(config('moonshine.templates.footer', 'moonshine::layouts.shared.footer'))
                @show
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
