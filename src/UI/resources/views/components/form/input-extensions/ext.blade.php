@props([
    'escapeSuffix' => null,
    'value',
    'valueHtml' => null,
])
<span {{ $attributes->class(['expansion']) }}><x-moonshine::display-text :value="$valueHtml ?? $value" :escape="$escapeSuffix" :display-type="\MoonShine\UI\Enums\DisplayTextType::SUFFIX" /></span>
