<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: $persist(false) }"
      x-bind:class="darkMode ? 'dark' : '' "
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('vendor/moonshine/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('vendor/moonshine/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('vendor/moonshine/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('vendor/moonshine/site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('vendor/moonshine/safari-pinned-tab.svg') }}" color="#7665FF">
        <meta name="msapplication-TileColor" content="#7665FF">
        <meta name="theme-color" content="#7665FF">

        <link rel="stylesheet" type="text/css" href="{{  mix('/css/moonshine.css', 'vendor/moonshine') }}">
        <script src="{{ mix('/js/moonshine.js', 'vendor/moonshine') }}" defer></script>

        @if(isset($resource) && $resource->getAssets('css'))
            @foreach($resource->getAssets('css') as $css)
                <link rel="stylesheet" href="{{ asset($css) }}">
            @endforeach
        @endif

		@yield('after-styles')

        @if(isset($resource) && $resource->getAssets('js'))
            @foreach($resource->getAssets('js') as $js)
                <script src="{{ asset($js) }}"></script>
            @endforeach
        @endif

        @yield('after-scripts')

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body x-cloak class="bg-whiteblue dark:bg-dark text-black dark:text-white">
        <div>
            @include('moonshine::shared.alert')

            <div x-data="{ sidebarOpen: false }" class="flex h-screen">
                @section("sidebar")
                    @include('moonshine::shared.sidebar')
                @show

                <div class="flex-1 flex flex-col overflow-hidden">
                    @section("header")
                        @include('moonshine::shared.header')
                    @show

                    <main class="flex-1 overflow-x-hidden overflow-y-auto">
                        <div class="mx-auto py-8 px-8">
                            @yield('content')
                        </div>
                    </main>
                </div>
            </div>
        </div>

        @include('moonshine::shared.popups')
    </body>
</html>
