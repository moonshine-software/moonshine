@props([
    'escapeLabel' => null,
    'label',
    'labelHtml' => null,
])
<li {{ $attributes->class('menu-divider') }}>
    @if($label)
        <span><x-moonshine::display-text :value="$labelHtml ?? $label" :escape="$escapeLabel" /></span>
    @endif
</li>
