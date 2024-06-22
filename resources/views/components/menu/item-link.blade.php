@props([
    'label' => '',
    'url' => '#',
    'icon' => '',
    'badge' => false,
    'top' => false,
])
<a
    href="{{ $url }}"
    {{ $attributes?->merge(['class' => 'menu-inner-link']) }}
>
    @if($icon)
        {!! $icon!!}
    @elseif(!$top)
        <span class="menu-inner-item-char">
            {{ str($label)->limit(2) }}
        </span>
    @endif

    <span class="menu-inner-text">{{ $label }}</span>

    @if($badge !== false)
        <span class="menu-inner-counter">{{ $badge }}</span>
    @endif
</a>
