<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('moonshine::layouts.shared.head')

        @vite(['resources/js/app.js'], 'vendor/moonshine')
    </head>
    <body>
        @include('moonshine::layouts.shared.flash')
        @yield('content')
    </body>
</html>
