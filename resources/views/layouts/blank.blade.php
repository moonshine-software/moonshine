<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{}">
<head>
@include('moonshine::layouts.shared.head')
</head>
<body>
@yield('content')
</body>
</html>
