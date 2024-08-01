@props([
    'data' => $_data ?? [],
    'top' => $isTop ?? false,
    'isScrollTo' => $isScrollTo ?? true,
])
@if($data)
    <ul {{ $attributes->class(['menu-inner']) }}
        @if(!$top && $isScrollTo)
            x-init="$nextTick(() => $el.querySelector('.menu-inner-item._is-active')?.scrollIntoView())"
        @endif
    >
        @foreach($data as $item)
            @if($item->hasCustomView())
                @include($item->getCustomView(), ['item' => $item, 'top' => $top])
            @elseif($item->isGroup())
                <x-moonshine::menu.group
                    :item="$item"
                    :top="$top"
                />
            @else
                <x-moonshine::menu.item
                    :item="$item"
                    :top="$top"
                />
            @endif
        @endforeach
    </ul>
@endif

{{ $slot ?? '' }}
