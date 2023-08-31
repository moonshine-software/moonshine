<meta charset="utf-8" />

<title>@yield('title', config("moonshine.title"))</title>

<meta name="description"
      content="{{ config("moonshine.title") }}"
/>

<meta name="viewport"
      content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"
/>

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('vendor/moonshine/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('vendor/moonshine/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('vendor/moonshine/favicon-16x16.png') }}">

<link rel="manifest" href="{{ asset('vendor/moonshine/site.webmanifest') }}">
<link rel="mask-icon"
      href="{{ asset('vendor/moonshine/safari-pinned-tab.svg') }}"
      color="{{ moonshineAssets()->getColor('primary') }}"
>

<meta name="msapplication-TileColor" content="{{ moonshineAssets()->getColor('primary') }}">
<meta name="theme-color" content="{{ moonshineAssets()->getColor('primary') }}">
