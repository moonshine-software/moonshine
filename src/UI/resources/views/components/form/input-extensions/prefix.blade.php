@props([
    'escapePrefix' => null,
    'value' => '',
    'valueHtml' => null,
])

<span {{ $attributes->class(['expansion', 'expansion--prefix']) }}>
    <x-moonshine::display-text :value="$valueHtml ?? $value" :escape="$escapePrefix" :display-type="\MoonShine\UI\Enums\DisplayTextType::PREFIX" />
</span>
