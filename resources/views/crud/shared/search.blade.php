<!-- Search -->
<div class="search" x-data="{ toggleSearch: false }">
    <form action="{{ $resource->currentRoute() }}"
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
            value="{{ request('search', '') }}"
            placeholder="{{ trans('moonshine::ui.search') }} (Ctrl+K)"
        />

        <svg @click.prevent="$refs.searchForm.submit()" class="search-form-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>

        <button @click.prevent="toggleSearch = ! toggleSearch" class="search-form-close block md:hidden" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </form>
    <div class="inline-flex items-center md:hidden">
        <button @click.prevent="toggleSearch = ! toggleSearch" class="text-slate-600 hover:text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </button>
    </div>
</div>
<!-- END: Search -->
