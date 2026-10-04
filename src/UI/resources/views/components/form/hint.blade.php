@props([
    'escapeHint' => null,
    'hintHtml' => null,
])
<div {{ $attributes->class(['form-hint']) }}>@if(! is_null($hintHtml)){{ $hintHtml }}@else<x-moonshine::display-text :value="$slot ?? ''" :escape="$escapeHint" :display-type="\MoonShine\UI\Enums\DisplayTextType::HINT" />@endif</div>
