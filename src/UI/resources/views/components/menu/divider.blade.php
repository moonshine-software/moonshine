@props([
    'escapeLabel' => null,
    'label',
    'labelHtml' => null,
])
<li {{ $attributes->class('menu-divider') }}>
    @if($label)
        <span>@if(! is_null($labelHtml)){{ $labelHtml }}@else<x-moonshine::display-text :value="$label" :escape="$escapeLabel" />@endif</span>
    @endif
</li>
