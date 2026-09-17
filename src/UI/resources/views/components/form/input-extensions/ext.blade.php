@props([
    'escapeSuffix' => moonshine()->getConfig()->isEscapeSuffix(),
    'value'
])
<span {{ $attributes->class(['expansion']) }}>{!! $escapeSuffix ? e($value) : $value !!}</span>
