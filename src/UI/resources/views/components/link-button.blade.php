@props([
    'label' => '',
    'labelHtml' => null,
    'icon' => null,
    'filled' => false,
    'badge' => false,
    'raw' => false,
])
<a {{ $attributes->class($raw ? [] : ['btn', 'btn-primary' => $filled]) }}>
    {{ $icon ?? '' }}
    @if($slot->isNotEmpty())
        {{ $slot }}
    @else
        {{ $labelHtml ?? $label }}
    @endif
    @if($badge !== false)
        <x-moonshine::badge color="">{{ $badge }}</x-moonshine::badge>
    @endif
</a>
