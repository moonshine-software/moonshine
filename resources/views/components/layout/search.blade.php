@props([
    'enabled' => true,
    'global' => false,
    'action' => '',
    'key' => 'search',
    'placeholder' => __('moonshine::ui.search') . ' (Ctrl+K)'
])
@if($enabled && !$global)
    <div {{ $attributes->class(['search']) }} x-data="{ toggleSearch: false }">
        <form action="{{ $action }}"
              x-ref="searchForm"
              class="search-form hidden md:block"
              :class="toggleSearch && '_is-toggled'"
        >
            <x-moonshine::form.input
                x-data="{}"
                x-ref="searchInput"
                name="search"
                @keyup.ctrl.k.window="$refs.searchInput.focus()"
                @keyup.ctrl.period.window="$refs.searchInput.focus()"
                type="search"
                class="search-form-field"
                value="{{ request($key, '') }}"
                placeholder="{{ $placeholder }}"
            />

            <x-moonshine::icon
                @click.prevent="$refs.searchForm.submit()"
                class="search-form-icon"
                icon="heroicons.outline.magnifying-glass"
                size="6"
            />

            <button @click.prevent="toggleSearch = ! toggleSearch"
                    class="search-form-close block md:hidden"
                    type="button"
            >
                <x-moonshine::icon icon="heroicons.outline.x-mark" />
            </button>
        </form>

        <div class="inline-flex items-center md:hidden">
            <button @click.prevent="toggleSearch = ! toggleSearch"
                    type="button"
                    class="text-slate-600 hover:text-secondary"
            >
                <x-moonshine::icon icon="heroicons.outline.magnifying-glass" size="6" />
            </button>
        </div>
    </div>
@endif

@if($enabled && $global)
    <div x-data="globalSearch(`{{ $action }}`)">
        <a
            @click.prevent="modal"
            @keyup.ctrl.k.window="modal"
            @keyup.ctrl.period.window="modal"
        >
            <x-moonshine::icon
                icon="heroicons.outline.magnifying-glass"
                size="6"
            />
        </a>

        <x-moonshine::modal eventName="global-search" title="Search" :closeOutside="true">
            <x-moonshine::form name="search">
                <x-moonshine::form.input
                    class="search-input"
                    x-model.debounce.300ms="query"
                />
            </x-moonshine::form>

            <div class="search-loading" style="display: none;">
                <x-moonshine::loader />
            </div>

            <ul>
                <template x-for="(data, key) in groups" :key="key">
                    <li x-html="group(key, data)" />
                </template>
            </ul>
        </x-moonshine::modal>
    </div>
@endif
