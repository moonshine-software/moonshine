@props([
    'color' => null,
    'icon' => null,
    'valueRaw' => false,
    'escapeUi' => false,
])
<span {{ $attributes->merge(['class' => 'badge'.($color ? ' badge-'.$color : '')])->class(['inline-flex items-center gap-1 max-w-full' => $icon?->isNotEmpty()]) }}>
    {{ $icon ?? '' }}

    @if(! $escapeUi || $valueRaw)
        {!! $slot !!}
    @else
        {!! e((string) $slot) !!}
    @endif
</span>
