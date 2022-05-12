<div x-data="{ open: {{ $item->isActive() ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="w-full flex justify-between items-center py-3 px-6 text-black dark:text-white
            cursor-pointer hover:bg-gray-700 hover:text-gray-100 focus:outline-none"
    >
        <span class="flex items-center">
            {!! $item->getIcon(6, 'purple') !!}

            <span class="mx-4">{{ $item->title() }}</span>
        </span>

        <span>
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path x-show="!open" d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"></path>
                <path x-show="open" d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </span>
    </button>

    @if($item->items())
        <div x-show="open" class="bg-gray-700">
            @foreach($item->items() as $child)
                <a href="{{ route($child->resource()->routeName('index')) }}"
                   class="py-2 px-16 block text-sm text-gray-100 {{ $child->isActive() ? 'bg-purple text-white' : 'hover:bg-purple hover:text-white' }}">
                    {{ $child->title() }}
                </a>
            @endforeach
        </div>
    @endif
</div>