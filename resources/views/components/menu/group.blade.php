@props([
    'label' => '',
    'icon' => '',
    'items' => [],
    'isActive' => false,
    'top' => false,
])
<li {{ $attributes->class(['menu-inner-item', 'dropdown' => $top]) }}
    @if($top)
        x-data="dropdown"
        x-ref="dropdownEl"
        @click.outside="closeDropdown"
        data-dropdown-placement="bottom-start"
    @else
        x-data="{ dropdown: {{ $isActive ? 'true' : 'false' }} }"
    @endif
>
    <button
        @if($top)
            @click="toggleDropdown"
            class="menu-inner-button dropdown-btn"
            :class="open && '_is-active'"
        @else
            x-data="navTooltip"
            @mouseenter="toggleTooltip()"
            @click.prevent="dropdown = ! dropdown"
            class="menu-inner-button"
            :class="dropdown && '_is-active'"
        @endif
        type="button"
    >
        @if($icon)
            {!! $icon !!}
        @elseif(!$top)
            <span class="menu-inner-item-char">
                {{ str($label)->limit(2) }}
            </span>
        @endif

        <span class="menu-inner-text">{{ $label }}</span>
        <span class="menu-inner-arrow">
            <x-moonshine::icon
                icon="heroicons.chevron-down"
                size="6"
                color="gray"
            />
        </span>
    </button>

    @if($items)
        @if($top)
            <x-moonshine::menu
                :dropdown="true"
                :items="$items"
                class="dropdown-body"
            />
        @else
            <x-moonshine::menu
                :dropdown="true"
                :items="$items"
                x-transition.top=""
                style="display: none"
                x-show="dropdown"
            />
        @endif
    @endif
</li>
