@props([
    'components' => [],
    'collapsed' => false,
    'collapseAttributes',
    'translates' => [],
])
<!-- Sidebar -->
<aside {{ $attributes->merge(['class' => 'layout-menu']) }}
       :class="{ '_is-minimized': minimizedMenu, '_is-opened': $store.menu.isSidebarOpen }"
>
    <x-moonshine::components
        :components="$components"
    />

    @if($collapsed)
        <!-- Collapse menu -->
        <div {{ $collapseAttributes->merge(['class' => 'layout-collapse']) }}>
            <button
                type="button"
                @click.prevent="minimizedMenu = ! minimizedMenu"
                class="layout-collapse-btn btn"
                title="{{ $translates['collapse_menu'] ?? 'Collapse sidebar' }}"
            >
                <x-moonshine::icon
                    icon="chevron-left"
                    x-show="!minimizedMenu"
                />
                <x-moonshine::icon
                    icon="chevron-right"
                    x-show="minimizedMenu"
                />
            </button>
        </div>
    @endif

    {{ $slot ?? '' }}
</aside>
<!-- END: Sidebar -->
