@props([
    'color' => null,
    'icon' => null,
    'value' => '',
    'valueRaw' => false,
])
<span {{ $attributes->merge(['class' => 'badge'.($color ? ' badge-'.$color : '')])->class(['inline-flex items-center gap-1 max-w-full' => $icon?->isNotEmpty()]) }}>
    {{ $icon ?? '' }}

    @if($valueRaw)
        {!! $value !!}
    @else
        {{ $value }}
    @endif
</span>
