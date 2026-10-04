@props([
    'escapePrefix' => null,
    'value' => '',
    'valueHtml' => null,
])

<span {{ $attributes->class(['expansion', 'expansion--prefix']) }}>
    @if(! is_null($valueHtml))
        {{ $valueHtml }}
    @else
        <x-moonshine::display-text :value="$value" :escape="$escapePrefix" :display-type="\MoonShine\UI\Enums\DisplayTextType::PREFIX" />
    @endif
</span>
