@props([
    'item'
])
<li class="menu-inner-item {{ $item->isActive() ? '_is-active' : '' }}"
    id="menu_item_{{ str($item->url())->remove(request()->schemeAndHttpHost())->slug('_') }}"
>
    <a href="{{ $item->url() }}" class="menu-inner-link">
        {!! $item->getIcon(6) !!}

        <span class="menu-inner-text">{{ $item->label() }}</span>

        @if($item->hasBadge())
            <span class="menu-inner-counter">{{ $item->getBadge() }}</span>
        @endif
    </a>
</li>
