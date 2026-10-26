@props([
    'hintHtml' => null,
])
<div {{ $attributes->class(['form-hint']) }}>{{ $hintHtml ?? $slot ?? '' }}</div>
