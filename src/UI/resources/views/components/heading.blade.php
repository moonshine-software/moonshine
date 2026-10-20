@props([
    'escapeLabel' => null,
    'label' => '',
    'labelHtml' => null,
    'tag' => 'h1',
])
<div class="heading">
    <{{ $tag }} {{ $attributes }}>
        <x-moonshine::display-text :value="$labelHtml ?? $label" :fallback="$slot ?? ''" :escape="$escapeLabel" :display-type="\MoonShine\UI\Enums\DisplayTextType::RECORD" />
    </{{ $tag }}>
</div>
