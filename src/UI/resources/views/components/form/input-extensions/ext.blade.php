@props([
    'value',
    'valueHtml' => null,
])
<span {{ $attributes->class(['expansion']) }}>{{ $valueHtml ?? $value }}</span>
