@props([
    'src',
    'alt' => null,
    'width' => null,
    'height' => null,
    'srcset' => null,
    'sizes' => null,
    'loading' => null, // eager, lazy
    'decoded' => null, // auto, async, sync
])

<img {{ $attributes->merge([
    'src' => $src,
    'alt' => $alt,
    'width' => $width,
    'height' => $height,
    'loadimg' => $loading,
    'decoded' => $decoded,
    'srcset' => $srcset,
    'sizes' => $sizes
]) }}>