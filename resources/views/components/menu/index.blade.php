@if($data)
    <ul {{ $attributes->class(['menu-inner', 'grow']) }}
        @if(!request()->routeIs('moonshine.index') && !request()->routeIs('moonshine.custom_page'))
            x-init="$nextTick(() => document.querySelector('.menu-inner-item._is-active').scrollIntoView())"
        @endif
    >
        @foreach($data as $item)
            <x-dynamic-component
                component="moonshine::menu.{{ $item->isGroup() ? 'group' : 'item' }}"
                :item="$item"
            />
        @endforeach
    </ul>
@endif
