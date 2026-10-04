@props([
    'escapeLabel' => null,
    'label' => '',
    'labelHtml' => null,
    'centered' => false
])
@if($label)
    <div {{ $attributes->class(['divider', 'divider-centered' => $centered]) }}>
        @if(! is_null($labelHtml))
            {{ $labelHtml }}
        @else
            <x-moonshine::display-text :value="$label" :escape="$escapeLabel" />
        @endif
    </div>
@else
    <hr {{ $attributes->class(['divider']) }} />
@endif
