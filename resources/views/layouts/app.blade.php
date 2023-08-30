@php use MoonShine\Components\Layout\Content;use MoonShine\Components\Layout\Flash;use MoonShine\Components\Layout\Footer;use MoonShine\Components\Layout\Header;use MoonShine\Components\Layout\LayoutBlock;use MoonShine\Components\Layout\LayoutBuilder;use MoonShine\Components\Layout\Menu;use MoonShine\Components\Layout\Sidebar; @endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>
<head>
    @include('moonshine::layouts.shared.head')
    @moonShineAssets
</head>
<body x-cloak
      x-data="{ minimizedMenu: $persist(false).as('minimizedMenu'), asideMenuOpen: false }"
>
{{ moonshineLayout() }}
@stack('scripts')
@include('moonshine::ui.img-popup')
</body>
</html>
