@props([
    'escapeHint' => null,
    'hintHtml' => null,
])
<div {{ $attributes->class(['form-hint']) }}><x-moonshine::display-text :value="$hintHtml ?? $slot ?? ''" :escape="$escapeHint" :display-type="\MoonShine\UI\Enums\DisplayTextType::HINT" /></div>
