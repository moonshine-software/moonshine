@props([
    'escapeLabel' => moonshine()->getConfig()->isEscapeLabel(),
    'label',
])
<li {{ $attributes->class('menu-divider') }}>
    @if($label)
        <span>{!! $escapeLabel ? e($label) : $label !!}</span>
    @endif
</li>
