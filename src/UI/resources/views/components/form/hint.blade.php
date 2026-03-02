@props([
    'escape' => false,
    'raw' => false,
])
<div {{ $attributes->class(['form-hint']) }}>
    @if(! $escape || $raw)
        {!! $slot ?? '' !!}
    @else
        {{ $slot ?? '' }}
    @endif
</div>
