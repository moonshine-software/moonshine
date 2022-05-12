<div>
    <a href="{{ route($item->resource()->routeName('index')) }}"
       class="w-full flex justify-between items-center py-3 px-6
       text-black dark:text-white cursor-pointer hover:bg-gray-700 hover:text-gray-100 focus:outline-none"
    >
        <span class="flex items-center">
            {!! $item->getIcon(6, 'purple') !!}

            <span class="mx-4 font-medium">{{ $item->title() }}</span>
        </span>
    </a>
</div>