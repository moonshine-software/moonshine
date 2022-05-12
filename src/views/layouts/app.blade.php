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

        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('vendor/moonshine/apple-touch-icon.png') }}"/>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('vendor/moonshine/favicon-32x32.png') }}"/>
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('vendor/moonshine/favicon-16x16.png') }}"/>

        <link rel="stylesheet" href="{{ asset('vendor/moonshine/css/compiled/app.css') }}">
        <script src="{{ asset('vendor/moonshine/js/compiled/app.js') }}" defer></script>

        @if(isset($resource) && $resource->getAssets("css"))
            @foreach($resource->getAssets("css") as $css)
                <link rel="stylesheet" href="{{ asset($css) }}">
            @endforeach
        @endif

		@yield('after-styles')

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <div class="bg-dark bg-darkblue bg-pink bg-purple bg-white bg-black
text-dark text-darkblue text-pink text-purple text-white text-black
bg-gray-100 bg-gray-200 bg-gray-300 bg-gray-400 bg-gray-500 bg-gray-600 bg-gray-700 bg-gray-800 bg-gray-900
text-gray-100 text-gray-200 text-gray-300 text-gray-400 text-gray-500 text-gray-600 text-gray-700 text-gray-800 text-gray-900
"></div>
    <body x-cloak class="bg-white dark:bg-black text-black dark:text-white">
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

                    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-darkblue">
                        <div class="mx-auto py-8 px-8">
                            @yield('content')
                        </div>
                    </main>
                </div>
            </div>
        </div>

        @include('moonshine::shared.popups')

		@yield('after-scripts')

        @if(isset($resource) && $resource->getAssets("js"))
            @foreach($resource->getAssets("js") as $js)
                <script src="{{ asset($js) }}"></script>
            @endforeach
        @endif
    </body>
</html>
