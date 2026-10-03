@props([
    'escapeLabel' => null,
    'label',
])
<li {{ $attributes->class('menu-divider') }}>
    @if($label)
        <span><x-moonshine::display-text :value="$label" :escape="$escapeLabel" /></span>
    @endif
</li>
