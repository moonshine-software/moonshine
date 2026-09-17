@props([
    'escapeLabel' => moonshine()->getConfig()->isEscapeLabel(),
    'label' => '',
    'tag' => 'h1',
])
<div class="heading">
    <{{ $tag }} {{ $attributes }}>
        {!! $label !== '' ? ($escapeLabel ? e($label) : $label) : ($slot ?? '') !!}
    </{{ $tag }}>
</div>
