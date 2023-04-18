@if ($paginator->hasPages())
    <!-- Pagination -->
    <div class="pagination">
        <ul class="pagination-list simple">
            {{-- Previous Page Link --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="pagination-simple disabled">
                        {!! __('moonshine::pagination.previous') !!}
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination-simple">
                        {!! __('moonshine::pagination.previous') !!}
                    </a>
                @endif
            </li>

            {{-- Next Page Link --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination-simple">
                        {!! __('moonshine::pagination.next') !!}
                    </a>
                @else
                    <span class="pagination-simple disabled">
                        {!! __('moonshine::pagination.next') !!}
                    </span>
                @endif
            </li>
        </ul>
    </div>
    <!-- END: Pagination -->
@endif
