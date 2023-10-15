<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data :class="$store.darkMode.on && 'dark'">
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

@include('moonshine::ui.img-popup')
@include('moonshine::ui.toasts')

@stack('scripts')
</body>
</html>
