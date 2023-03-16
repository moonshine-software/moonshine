@if($data)
    <ul class="menu-inner mt-4 grow">
        @foreach($data as $item)
            <x-dynamic-component
                component="moonshine::menu.{{ $item->isGroup() ? 'group' : 'item' }}"
                :item="$item"
            />
        @endforeach
    </ul>
@endif
