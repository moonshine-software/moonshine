@props([
    'escapeLabel' => null,
    'label' => '',
    'tag' => 'h1',
])
<div class="heading">
    <{{ $tag }} {{ $attributes }}>
        <x-moonshine::display-text :value="$label" :fallback="$slot ?? ''" :escape="$escapeLabel" />
    </{{ $tag }}>
</div>
