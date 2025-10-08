@props([
    'label' => '',
    'previewLabel' => '',
    'icon' => '',
    'items' => [],
    'isActive' => false,
    'top' => false,
])
<li {{ $attributes->class(['menu-item']) }}
    @if($top)
        x-data="{ dropdown: false }"
        @click.outside="dropdown = false"
        data-dropdown-placement="bottom-start"
    @else
        x-data="{ dropdown: {{ $isActive ? 'true' : 'false' }} }"
    @endif
    :class="dropdown && 'menu-item--opened'"
    x-ref="dropdownMenu"
>
    <button
        @if(!$top)
            x-data="navTooltip"
            @mouseenter="toggleTooltip()"
        @endif
        @click.prevent="dropdown = ! dropdown; $nextTick(() => { if (dropdown && $refs.dropdownMenu) $refs.dropdownMenu.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); })"
        class="menu-button"
        :class="dropdown && '_is-active'"
        type="button"
    >
        @if($icon)
            <div class="menu-icon">
                {!! $icon !!}
            </div>
        @else
            <span class="menu-char">
                {{ $previewLabel }}
            </span>
        @endif

        <span class="menu-text">{{ $label }}</span>
        <span class="menu-arrow">
            <x-moonshine::icon
                icon="chevron-down"
            />
        </span>
    </button>

    @if($items)
        <x-moonshine::menu
            :dropdown="true"
            :items="$items"
            x-transition.top=""
            style="display: none"
            x-show="dropdown"
        />
    @endif
</li>
