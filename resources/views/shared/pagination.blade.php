@if ($paginator->hasPages())
    <div class="hidden sm:block">
        <p class="text-sm text-purple dark:text-white">
            {!! __('moonshine::pagination.showing') !!}
            @if ($paginator->firstItem())
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                {!! __('moonshine::pagination.to') !!}
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('moonshine::pagination.of') !!}
            <span class="font-medium">{{ $paginator->total() }}</span>
            {!! __('moonshine::pagination.results') !!}
        </p>
    </div>

    <div class="sm:hidden flex flex-1 justify-between my-4">
        @if (!$paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}"
               class="relative inline-flex items-center rounded-md border px-4 py-2 text-sm font-medium border-purple bg-transparent text-purple hover:text-white hover:bg-purple">
                {!! __('moonshine::pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="relative ml-3 inline-flex items-center rounded-md border px-4 py-2 text-sm font-medium border-purple bg-transparent text-purple hover:text-white hover:bg-purple">
                {!! __('moonshine::pagination.next') !!}
            </a>
        @endif
    </div>


    <nav class="hidden sm:inline-flex sm:-space-x-px my-4" aria-label="Pagination">
        @if (!$paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}"
               class="relative inline-flex items-center rounded-l-md border px-2 py-2 text-sm font-medium focus:z-20 border-purple bg-transparent text-purple hover:text-white hover:bg-purple">
                <span class="sr-only">{!! __('moonshine::pagination.previous') !!}</span>
                <!-- Heroicon name: mini/chevron-left -->
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                     aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                          clip-rule="evenodd"/>
                </svg>
            </a>
        @endif

        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span
                    class="relative inline-flex items-center border px-4 py-2 text-sm font-medium border-purple bg-transparent text-purple hover:text-white hover:bg-purple">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <a href="#"
                           class="relative z-10 inline-flex items-center border px-4 py-2 text-sm font-medium focus:z-20 border-pink bg-transparent text-pink hover:text-white hover:bg-pink">
                            {{ $page }}
                        </a>
                    @else
                        <a href="{{ $url }}"
                           class="relative items-center border px-4 py-2 text-sm font-medium focus:z-20 md:inline-flex border-purple bg-transparent text-purple hover:text-white hover:bg-purple">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="relative inline-flex items-center rounded-r-md border px-2 py-2 text-sm font-medium focus:z-20 border-purple bg-transparent text-purple hover:text-white hover:bg-purple">
                <span class="sr-only">{!! __('moonshine::pagination.next') !!}</span>
                <!-- Heroicon name: mini/chevron-right -->
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                     aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                          clip-rule="evenodd"/>
                </svg>
            </a>
        @endif
    </nav>
@endif
