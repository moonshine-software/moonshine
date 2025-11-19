@props([
    'src',
    'alt' => null,
    'width' => null,
    'height' => null,
    'srcset' => null,
    'loading' => null, // eager, lazy
    'decoding' => null, // auto, async, sync
])

<img {{ $attributes->merge([
    'src' => $src,
    'alt' => $alt,
    'width' => $width,
    'height' => $height,
    'loadimg' => $loading,
    'decoding' => $decoding,
    'srcset' => $srcset
]) }}>
