@props([
    'escapeLabel' => null,
    'label' => '',
    'labelHtml' => null,
])
<fieldset {{ $attributes }}>
    <legend>@if(! is_null($labelHtml)){{ $labelHtml }}@else<x-moonshine::display-text :value="$label" :escape="$escapeLabel" />@endif</legend>

    {{ $slot }}
</fieldset>
