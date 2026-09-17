@props([
    'escapeLabel' => moonshine()->getConfig()->isEscapeLabel(),
    'label' => '',
    'centered' => false
])
@if($label)
    <div {{ $attributes->class(['divider', 'divider-centered' => $centered]) }}>
        {!! $escapeLabel ? e($label) : $label !!}
    </div>
@else
    <hr {{ $attributes->class(['divider']) }} />
@endif
