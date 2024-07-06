@props([
    'title' => '',
    'bodyColor' => '',
    'components' => []
])
<head {{ $attributes }}
>
    <meta charset="utf-8" />

    <title>@yield('title', $title)</title>

    <meta name="description"
          content="{{ $title }}"
    />

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"
    />

    <meta name="msapplication-TileColor" content="{{ $bodyColor }}">
    <meta name="theme-color" content="{{ $bodyColor }}">

    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</head>
