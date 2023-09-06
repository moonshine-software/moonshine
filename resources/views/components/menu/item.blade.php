@props([
    'item',
    'top' => false,
])
@if($item instanceof MoonShine\Menu\MenuDivider)
    <li class="menu-inner-divider">
        {!! $item->label() ? "<span>{$item->label()}</span>" : '' !!}
    </li>
@else
    <li class="menu-inner-item {{ $item->isActive() ? '_is-active' : '' }}">
        <a href="{{ $item->url() }}" class="menu-inner-link" x-data="navTooltip" @mouseenter="toggleTooltip()">
            @if($item->iconValue())
                {!! $item->getIcon(6) !!}
            @else
                <span class="menu-inner-item-char">
                    {{ str($item->label())->limit(2) }}
                </span>
            @endif

            <span class="menu-inner-text">{{ $item->label() }}</span>

            @if($item->hasBadge())
                <span class="menu-inner-counter">{{ $item->getBadge() }}</span>
            @endif
        </a>
    </li>
@endif
