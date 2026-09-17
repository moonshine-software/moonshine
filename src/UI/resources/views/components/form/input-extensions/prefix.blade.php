@props([
    'escapePrefix' => moonshine()->getConfig()->isEscapePrefix(),
    'value' => '',
])

<span {{ $attributes->class(['expansion', 'expansion--prefix']) }}>
    {!! $escapePrefix ? e($value) : $value !!}
</span>
