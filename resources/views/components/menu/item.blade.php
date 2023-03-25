@props([
    'item'
])
<li class="menu-inner-item {{ $item->isActive() ? '_is-active' : '' }}">
    <a href="{{ $item->url() }}" class="menu-inner-link">
        {!! $item->getIcon(6) !!}

        <span class="menu-inner-text">{{ $item->title() }}</span>

        @if($item->hasBadge())
            <span class="menu-inner-counter">{{ $item->getBadge() }}</span>
        @endif
    </a>
</li>
