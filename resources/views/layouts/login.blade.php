<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('moonshine::layouts.shared.head')
        @moonShineAssets
    </head>
    <body>
        @yield('content')
    </body>
</html>
