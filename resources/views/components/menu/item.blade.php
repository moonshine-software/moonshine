@props([
    'label' => '',
    'icon' => '',
    'badge' => '',
    'url' => '#',
    'items' => [],
    'isActive' => false,
    'top' => false,
    'linkAttributes' => null,
])
<li
    {{ $attributes->class(['menu-inner-item', '_is-active' => $isActive]) }}
>
    <a
        href="{{ $url }}" {{ $linkAttributes?->merge(['class' => 'menu-inner-link']) }}
        x-data="navTooltip"
        @mouseenter="toggleTooltip()"
    >
        @if($icon)
            {!! $icon!!}
        @elseif(!$top)
            <span class="menu-inner-item-char">
                {{ str($label)->limit(2) }}
            </span>
        @endif

        <span class="menu-inner-text">{{ $label }}</span>

        @if($badge)
            <span class="menu-inner-counter">{{ $badge }}</span>
        @endif
    </a>
</li>
