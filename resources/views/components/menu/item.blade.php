@props([
    'item',
    'top' => false,
])
@if($item instanceof MoonShine\Menu\MenuDivider)
    <li {{ $item->attributes()->merge(['class' => 'menu-inner-divider']) }}>
        {!! $item->label() ? "<span>{$item->label()}</span>" : '' !!}
    </li>
@else
    <li {{ $item->attributes()->class(['menu-inner-item', '_is-active' => $item->isActive()]) }}>
        <a
            href="{{ $item->url() }}" {!! $item->isBlank() ? 'target="_blank"' : '' !!}
            {{ $item->linkAttributes()->merge(['class' => 'menu-inner-link']) }}
            x-data="navTooltip"
            @mouseenter="toggleTooltip()"
        >
            @if($item->iconValue())
                {!! $item->getIcon(6) !!}
            @elseif(!$top)
                <span class="menu-inner-item-char">
                    {{ str($item->label())->limit(2) }}
                </span>
            @endif

            <span class="menu-inner-text">{{ $item->label() }}</span>

            @if($item->hasBadge() && $badge = $item->getBadge())
                <span class="menu-inner-counter">{{ $badge }}</span>
            @endif
        </a>
    </li>
@endif
