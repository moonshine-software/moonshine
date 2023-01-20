<div class="flex items-center select-none" x-data="{}">
    <span @click="$refs.searchForm.submit()" class="hover:text-purple cursor-pointer mr-3">
        @include('moonshine::shared.icons.search', ['size' => 5, 'color' => 'purple'])
    </span>

    <form action="{{ $resource->currentRoute() }}" method="get" x-ref="searchForm">
        {{ csrf_field() }}

        <input value="{{ request("search") }}"
               aria-label="{{ trans('moonshine::ui.search') }}"
               name="search" class="bg-white dark:bg-darkblue focus:outline-none" type="text"
               placeholder="{{ trans('moonshine::ui.search') }}">
    </form>
</div>
