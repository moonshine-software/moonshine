@props([
    'components' => [],
    'translates' => [],
])
<div {{ $attributes->merge(['class' => 'layout-bottom-bar']) }}
    x-data="bottomBarMenu"
>
    <div class="layout-bottom-bar-content">
        <button
            type="button"
            class="layout-bottom-bar-back"
            x-show="showBackButton"
            @click="navigateBack()"
            x-cloak
        >
            <x-moonshine::icon icon="chevron-left" />
            <span class="layout-bottom-bar-back-text">{{ $translates['back'] ?? 'Back' }}</span>
        </button>

        <div class="layout-bottom-bar-items">
            <template x-for="(item, index) in currentItems" :key="index">
                <button
                    type="button"
                    class="layout-bottom-bar-item"
                    @click="handleItemClick(item)"
                    :class="{ '_is-active': item.isActive }"
                >
                    <div class="layout-bottom-bar-item-icon" x-html="item.icon"></div>
                    <div class="layout-bottom-bar-item-text" x-text="item.text"></div>
                    <div class="layout-bottom-bar-item-badge" x-show="item.badge" x-text="item.badge" x-cloak></div>
                </button>
            </template>
        </div>
    </div>

    <div style="display: none;" data-bottom-menu-source>
        <x-moonshine::components :components="$components" />
        {{ $slot ?? '' }}
    </div>
</div>
