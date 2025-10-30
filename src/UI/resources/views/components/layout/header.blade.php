@props([
    'components' => [],
    'translates' => [],
])
<div {{ $attributes->merge(['class' => 'layout-navigation']) }}>
    <button
        type="button"
        @click.prevent="minimizedMenu = ! minimizedMenu"
        class="collapse-sidebar-btn btn btn-square max-lg:hidden"
        title="{{ $translates['collapse_menu'] ?? 'Collapse sidebar' }}"
    >
        <div class="menu-icon" x-show="!minimizedMenu">
            <x-moonshine::icon icon="sidebar-close" />
        </div>

        <div class="menu-icon" x-cloak x-show="minimizedMenu">
            <x-moonshine::icon icon="sidebar-open" />
        </div>
    </button>

    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
