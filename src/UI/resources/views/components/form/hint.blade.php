@props(['escapeHint' => null])
<div {{ $attributes->class(['form-hint']) }}><x-moonshine::display-text :value="$slot ?? ''" :escape="$escapeHint" :display-type="\MoonShine\UI\Enums\DisplayTextType::HINT" /></div>
