@props([
    'item'
])
<li class="menu-inner-item"
    x-data="{ dropdown: {{ $item->isActive() ? 'true' : 'false' }} }"
>
    <button @click.prevent="dropdown = ! dropdown" class="menu-inner-button" :class="dropdown && '_is-active'" type="button">
        {!! $item->getIcon(6, 'white') !!}

        <span class="menu-inner-text">{{ $item->title() }}</span>
        <span class="menu-inner-arrow">
            <x-moonshine::icon
                icon="heroicons.chevron-down"
                size="6"
                color="gray"
            />
        </span>
    </button>

    @if($item->items())
        <!-- Dropdown Menu -->
        <ul class="menu-inner-dropdown" style="display: none" x-show="dropdown" x-transition.top>
            @foreach($item->items() as $child)
                <x-moonshine::menu.item :item="$child" />
            @endforeach
        </ul>
        <!-- END: Dropdown Menu -->
    @endif
</li>
