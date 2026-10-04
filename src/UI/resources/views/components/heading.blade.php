@props([
    'escapeLabel' => null,
    'label' => '',
    'labelHtml' => null,
    'tag' => 'h1',
])
<div class="heading">
    <{{ $tag }} {{ $attributes }}>
        @if($label !== '' && ! is_null($labelHtml))
            {{ $labelHtml }}
        @else
            <x-moonshine::display-text :value="$label" :fallback="$slot ?? ''" :escape="$escapeLabel" />
        @endif
    </{{ $tag }}>
</div>
