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
    {{ $slot ?? '' }}
    @if($badge !== false)
        <x-moonshine::badge color="">{{ $badge }}</x-moonshine::badge>
    @endif
</a>
