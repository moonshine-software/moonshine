@props([
    'escapePrefix' => null,
    'value' => '',
])

<span {{ $attributes->class(['expansion', 'expansion--prefix']) }}>
    <x-moonshine::display-text :value="$value" :escape="$escapePrefix" type="prefix" />
</span>
