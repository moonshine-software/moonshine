@props([
    'value' => '',
])

<span {{ $attributes->class(['expansion', 'expansion--prefix']) }}>
    {!! $value !!}
</span>
