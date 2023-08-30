<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>
<head>
    @include('moonshine::layouts.shared.head')
    @moonShineAssets
</head>
<body
    class="antialiased"
    x-cloak
    x-data="{ minimizedMenu: $persist(false).as('minimizedMenu'), asideMenuOpen: false }"
>
{{ moonshineLayout() }}
@stack('scripts')
@include('moonshine::ui.img-popup')
</body>
</html>
