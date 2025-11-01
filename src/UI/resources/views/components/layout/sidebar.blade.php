@props([
    'components' => [],
])
<aside {{ $attributes->merge(['class' => 'layout-menu']) }}
       :class="{ '_is-minimized': minimizedMenu, '_is-opened': asideMenuOpen }"
>
    <x-moonshine::components
        :components="$components"
    />

    <!-- Collapse menu -->
    <div class="layout-collapse">
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

    {{ $slot ?? '' }}
</aside>
