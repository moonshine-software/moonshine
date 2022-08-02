<div class="flex items-center select-none">
    <span class="hover:text-purple cursor-pointer mr-3">
        @include('moonshine::shared.icons.search', ['size' => 5, 'color' => 'purple'])
    </span>

    <form action="{{ $resource->route("index") }}" method="get">
        {{ csrf_field() }}

        <input value="{{ request("search") }}"
               aria-label="{{ trans('moonshine::ui.search') }}"
               name="search" class="bg-white dark:bg-darkblue focus:outline-none" type="text"
               placeholder="{{ trans('moonshine::ui.search') }}">
    </form>
</div>