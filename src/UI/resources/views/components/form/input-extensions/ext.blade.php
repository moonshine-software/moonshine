@props([
    'escapeSuffix' => null,
    'value',
    'valueHtml' => null,
])
<span {{ $attributes->class(['expansion']) }}>@if(! is_null($valueHtml)){{ $valueHtml }}@else<x-moonshine::display-text :value="$value" :escape="$escapeSuffix" :display-type="\MoonShine\UI\Enums\DisplayTextType::SUFFIX" />@endif</span>
