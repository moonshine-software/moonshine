@props([
    'value' => '',
    'valueHtml' => null,
])

<span {{ $attributes->class(['expansion', 'expansion--prefix']) }}>
    {{ $valueHtml ?? $value }}
</span>
