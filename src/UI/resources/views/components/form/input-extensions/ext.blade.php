@props([
    'escapeSuffix' => null,
    'value'
])
<span {{ $attributes->class(['expansion']) }}><x-moonshine::display-text :value="$value" :escape="$escapeSuffix" type="suffix" /></span>
