<div>
    <a href="{{ route($item->resource()->routeName('index')) }}"
       class="w-full flex justify-between items-center py-2 px-6 focus:outline-none
{{ $item->isActive() ? 'bg-gradient-to-r from-purple to-pink ' : '' }}
       text-white "
    >
        <span class="flex items-center">
            {!! $item->getIcon(6, 'white') !!}

            <span class="mx-4 font-medium">{{ $item->title() }}</span>
        </span>
    </a>
</div>