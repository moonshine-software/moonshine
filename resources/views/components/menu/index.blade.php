@props([
    'data' => $_data ?? [],
    'top' => $isTop ?? false,
])
@if($data)
    <ul {{ $attributes->class(['menu-inner', 'grow' => !$top]) }}
        @if(!$top)
            x-init="$nextTick(() => document.querySelector('.menu-inner-item._is-active')?.scrollIntoView())"
        @endif
    >
        @foreach($data as $item)
            @if($item->isGroup())
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
