@props([
    'label' => '',
    'tag' => 'h1',
])
<div class="heading">
    <{{ $tag }} {{ $attributes }}>
        {{ $label }}
    </{{ $tag }}>
</div>
