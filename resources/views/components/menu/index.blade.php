@props([
    'data',
    'top' => false
])
@if($data)
    <ul {{ $attributes->class(['menu-inner', 'grow' => !$top]) }}
        @if(!$top && !request()->routeIs('moonshine.index') && !request()->routeIs('moonshine.custom_page'))
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
