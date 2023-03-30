@if($data)
    <ul class="menu-inner mt-4 grow"
        @if(!request()->routeIs('moonshine.index'))
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
