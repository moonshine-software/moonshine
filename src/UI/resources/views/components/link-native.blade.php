@props([
    'label' => '',
    'labelHtml' => null,
    'escapeLabel' => null,
    'icon' => null,
    'filled' => false,
    'badge' => false,
])
<a {{ $attributes->class(['inline-flex items-center gap-1 max-w-full', 'text-primary' => $filled]) }}>
    {{ $icon ?? '' }}
    <x-moonshine::display-text :value="$slot ?? ''" :fallback="$labelHtml ?? $label" :escape="$escapeLabel" />
    @if($badge !== false)
        <x-moonshine::badge color="">{{ $badge }}</x-moonshine::badge>
    @endif
</a>
