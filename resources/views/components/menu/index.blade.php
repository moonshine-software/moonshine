@props([
    'data' => $_data ?? [],
    'top' => $isTop ?? false
])
@if($data)
    <ul {{ $attributes->class(['menu-inner', 'grow' => !$top]) }}
        @if(!$top)
            x-init="$nextTick(() => document.querySelector('.menu-inner-item._is-active')?.scrollIntoView())"
        @endif
    >
        @foreach($data as $item)
            <x-dynamic-component
                component="moonshine::menu.{{ $item->isGroup() ? 'group' : 'item' }}"
                :item="$item"
                :top="$top"
            />
        @endforeach
    </ul>
@endif

{{ $slot ?? '' }}
