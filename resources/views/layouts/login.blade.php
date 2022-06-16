<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config("moonshine.title") }}</title>

        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('vendor/moonshine/apple-touch-icon.png') }}"/>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('vendor/moonshine/favicon-32x32.png') }}"/>
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('vendor/moonshine/favicon-16x16.png') }}"/>

        <link rel="stylesheet" type="text/css" href="{{  mix('/css/moonshine.css', 'vendor/moonshine') }}">
        <script src="{{ mix('/js/moonshine.js', 'vendor/moonshine') }}" defer></script>

		@yield('after-styles')
    </head>
    <body>
        <div>
            @include('moonshine::shared.alert')

            @yield('content')
        </div>

		@yield('after-scripts')
    </body>
</html>
