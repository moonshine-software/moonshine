@props([
    'size' => 'sm',
    'color' => '',
    'fixed' => false,
    'absolute' => false
])

<div {{ $attributes->class([
    'spinner',
    "spinner-$size" => $size,
    "spinner--$color" => $color,
    'spinner-fixed' => $fixed,
    'spinner-absolute' => $absolute
]) }}></div>
