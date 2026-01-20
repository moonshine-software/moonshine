@props([
    'color' => null,
    'icon' => null,
])
<span {{ $attributes->merge(['class' => 'badge'.($color ? ' badge-'.$color : '')])->class(['inline-flex items-center gap-1 max-w-full' => $icon?->isNotEmpty()]) }}>{{ $icon ?? '' }}{{ $slot }}</span>
