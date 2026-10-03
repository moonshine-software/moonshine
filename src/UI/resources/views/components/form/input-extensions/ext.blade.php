@props([
    'escapeSuffix' => null,
    'value'
])
<span {{ $attributes->class(['expansion']) }}><x-moonshine::display-text :value="$value" :escape="$escapeSuffix" :display-type="\MoonShine\UI\Enums\DisplayTextType::SUFFIX" /></span>
