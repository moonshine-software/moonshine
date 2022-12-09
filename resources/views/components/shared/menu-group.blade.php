<div x-data="{ open: {{ $item->isActive() ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="w-full flex justify-between items-center py-2 px-6
            cursor-pointer focus:outline-none
            {{ $item->isActive() ? 'bg-gradient-to-r from-purple to-pink' : '' }}
            text-white "
    >
        <span class="flex items-center">
            {!! $item->getIcon(6, 'white') !!}

            <span class="mx-4">{{ $item->title() }}</span>

            @if($item->hasBadge())
                @include('moonshine::shared.badge', ['color' => 'purple', 'value' => $item->getBadge()])
            @endif
        </span>

        <span>
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path x-show="!open" d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"></path>
                <path x-show="open" d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </span>
    </button>

    @if($item->items())
        <div x-show="open" class="bg-darkblue">
            @foreach($item->items() as $child)
                <a href="{{ route($child->resource()->routeName('index')) }}"
                   class="py-2 px-10 block text-sm text-white {{ $child->isActive() ? 'font-bold' : '' }}">
                    <span class="flex items-center">
                        {!! $child->getIcon(4, 'white') !!}

                        <span class="mx-4">{{ $child->title() }}</span>

                        @if($child->hasBadge())
                            @include('moonshine::shared.badge', ['color' => 'purple', 'value' => $child->getBadge()])
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</div>
