@props([
    'enabled' => $isEnabled ?? true,
    'action' => '',
    'value' => '',
    'placeholder' => '',
])
@if($enabled)
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
                value="{{ $value }}"
                placeholder="{{ $placeholder }}"
            />

            <x-moonshine::icon
                @click.prevent="$refs.searchForm.submit()"
                class="search-form-icon"
                icon="magnifying-glass"
                size="6"
            />

            <button @click.prevent="toggleSearch = ! toggleSearch"
                    class="search-form-close block md:hidden"
                    type="button"
            >
                <x-moonshine::icon icon="x-mark" />
            </button>
        </form>

        <div class="inline-flex items-center md:hidden">
            <button @click.prevent="toggleSearch = ! toggleSearch"
                    type="button"
                    class="text-slate-600 hover:text-secondary"
            >
                <x-moonshine::icon icon="magnifying-glass" size="6" />
            </button>
        </div>
    </div>
@endif
