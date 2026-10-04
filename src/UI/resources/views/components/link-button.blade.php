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
    @if(is_null($escapeLabel) && trim((string) ($slot ?? '')) !== '')
        {{ $slot }}
    @elseif(! is_null($labelHtml))
        {{ $labelHtml }}
    @else
        <x-moonshine::display-text :value="$slot ?? ''" :fallback="$label" :escape="$escapeLabel" />
    @endif
    @if($badge !== false)
        <x-moonshine::badge color="">{{ $badge }}</x-moonshine::badge>
    @endif
</a>
