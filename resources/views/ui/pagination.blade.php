@if ($paginator->hasPages())
    <!-- Pagination -->
    <div class="pagination">
        <ul class="pagination-list">
            @if (!$paginator->onFirstPage())
                <li class="pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination-first" title="{!! __('moonshine::pagination.previous') !!}">
                        <x-moonshine::icon
                            icon="heroicons.chevron-double-left"
                            size="6"
                        />
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="pagination-item">
                        <span class="pagination-dots">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="pagination-item">
                            <a href="{{ $url }}" class="pagination-page @if ($page == $paginator->currentPage()) _is-active @endif">
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            @if (!$paginator->hasMorePages())
                <li class="pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination-last" title="{!! __('moonshine::pagination.next') !!}">
                        <x-moonshine::icon
                            icon="heroicons.chevron-double-right"
                            size="6"
                        />
                    </a>
                </li>
            @endif
        </ul>
        <div class="pagination-results">
            {!! __('moonshine::pagination.showing') !!}
            @if ($paginator->firstItem())
                {{ $paginator->firstItem() }}
                {!! __('moonshine::pagination.to') !!}
                {{ $paginator->lastItem() }}
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('moonshine::pagination.of') !!}
            {{ $paginator->total() }}
            {!! __('moonshine::pagination.results') !!}
        </div>
    </div>
    <!-- END: Pagination -->
@endif
