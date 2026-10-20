@props([
    'label' => '',
    'labelHtml' => null,
    'escapeLabel' => null,
    'icon' => null,
    'filled' => false,
    'badge' => false,
    'raw' => false,
])
<a {{ $attributes->class($raw ? [] : ['btn', 'btn-primary' => $filled]) }}>
    {{ $icon ?? '' }}
    <x-moonshine::display-text :value="$slot ?? ''" :fallback="$labelHtml ?? $label" :escape="$escapeLabel" />
    @if($badge !== false)
        <x-moonshine::badge color="">{{ $badge }}</x-moonshine::badge>
    @endif
</a>
