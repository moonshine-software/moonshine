@props([
    'escapePrefix' => null,
    'value' => '',
])

<span {{ $attributes->class(['expansion', 'expansion--prefix']) }}>
    <x-moonshine::display-text :value="$value" :escape="$escapePrefix" :display-type="\MoonShine\UI\Enums\DisplayTextType::PREFIX" />
</span>
