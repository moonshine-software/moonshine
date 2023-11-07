<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data
      :class="$store.darkMode.on && 'dark'"
>
<head>
    @include('moonshine::layouts.shared.head')
    @moonShineAssets
</head>
{{ moonshineLayout() }}
</html>
