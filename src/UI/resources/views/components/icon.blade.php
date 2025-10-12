@props([
    'path' => '',
    'icon' => '',
    'size' => '',
    'color' => '',
])
<div {{ $attributes->class(array_filter([
    'icon-wrapper',
    $size ? "size-$size" : null,
    empty($color) ? 'text-current' : "text-$color",
])) }}>
    @if($slot?->isNotEmpty())
        {!! $slot !!}
    @else
        @includeWhen($icon, "$path.$icon")
    @endif
</div>
