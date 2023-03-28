@if($data)
    <ul class="menu-inner mt-4 grow"
        @if(!request()->routeIs('moonshine.index'))
            x-init="$nextTick(() => document.getElementById('menu_item_{{ str(request()->path())->slug('_') }}').scrollIntoView())"
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
