@props([
    'components' => [],
    'collapsed' => false,
    'hasMenu' => false,
    'collapseAttributes',
    'translates' => [],
])
@if($hasMenu || ($slot ?? false))
<aside {{ $attributes->merge(['class' => 'layout-second-sidebar']) }}
       :class="{ '_is-minimized': minimizedMenu }"
>
    <x-moonshine::components :components="$components" />

    @if($collapsed)
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
@endif
