@props([
    'label' => '',
    'labelHtml' => null,
    'tag' => 'h1',
])
<div class="heading">
    <{{ $tag }} {{ $attributes }}>
        {{ $labelHtml ?? ($label !== '' ? $label : ($slot ?? '')) }}
    </{{ $tag }}>
</div>
