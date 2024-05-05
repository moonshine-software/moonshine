@props([
    'path' => '',
    'icon' => '',
    'size' => 5,
    'color' => '',
])
<div {{ $attributes->class([
    'text-current',
    'w-' . ($size ?? 5),
    'h-' . ($size ?? 5),
    "text-$color" => !empty($color),
]) }}>
    @if($slot?->isNotEmpty())
        {!! $slot !!}
    @else
        @includeWhen($icon, "$path.$icon")
    @endif
</div>
