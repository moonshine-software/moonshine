@props([
    'assets' => [],
    'bodyColor' => ''
])

@if(isset($assets['apple-touch']))
<link rel="apple-touch-icon"
      sizes="180x180"
      href="{{ $assets['apple-touch'] }}"
>
@endif

@if(isset($assets['32']))
<link rel="icon" type="image/png"
      sizes="32x32"
      href="{{ $assets['32'] }}"
>
@endif

@if(isset($assets['16']))
<link rel="icon" type="image/png" sizes="16x16"
      href="{{ $assets['16'] }}"
>
@endif

@if(isset($assets['web-manifest']))
<link rel="manifest"
      href="{{ $assets['web-manifest'] }}"
>
@endif

@if(isset($assets['safari-pinned-tab']))
<link rel="mask-icon"
      href="{{ $assets['safari-pinned-tab'] }}"
      color="{{ $bodyColor }}"
>
@endif

{{ $slot ?? '' }}
