@props([
    'color' => null
])
<span {{ $attributes->merge(['class' => 'badge'.($color ? ' badge-'.$color : '')]) }}>{{ $slot }}</span>
