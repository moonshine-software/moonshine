@props([
    'simple' => false,
    'async' => false,
    'has_pages' => false,
    'current_page' => 0,
    'last_page' => 0,
    'per_page' => 0,
    'first_page_url' => '',
    'next_page_url' => '',
    'prev_page_url' => '',
    'to' => 0,
    'from' => 0,
    'total' => 0,
    'links' => [],
    'translates' => [],
])

@if($simple)
    <!-- Pagination -->
    <div class="pagination">
        <ul class="pagination-list simple">
            {{-- Previous Page Link --}}
            <li>
                @if ($prev_page_url)
                    <a 
                        href="{{ $prev_page_url }}"
                        @if($async) @click.prevent="asyncRequest" @endif
                        class="pagination-link pagination-link--first"
                        title="{!! $translates['previous']  !!}"
                    >
                        {!! $translates['previous'] !!}
                    </a>
                @else
                    <span class="pagination-link _is-disabled">
                        {!! $translates['previous'] !!}
                    </span>
                @endif
            </li>

            {{-- Next Page Link --}}
            <li>
                @if ($next_page_url)
                    <a
                        href="{{ $next_page_url }}"
                        @if($async) @click.prevent="asyncRequest" @endif
                        class="pagination-link pagination-link--last"
                        title="{!! $translates['next']  !!}"
                    >
                        {!! $translates['next'] !!}
                    </a>
                @else
                    <span class="pagination-link _is-disabled">
                        {!! $translates['next'] !!}
                    </span>
                @endif
            </li>
        </ul>
    </div>
    <!-- END: Pagination -->
@elseif ($has_pages)
    <!-- Pagination -->
    <div class="pagination">
        <ul class="pagination-list">
            @if ($current_page > 1)
                <li class="pagination-item">
                    <a href="{{ $prev_page_url }}"
                       @if($async) @click.prevent="asyncRequest" @endif
                       class="pagination-link pagination-link--first"
                       title="{!! $translates['previous']  !!}"
                    >
                        <x-moonshine::icon icon="chevron-double-left" />
                    </a>
                </li>
            @endif

            @foreach ($links as $link)
                {{-- "Three Dots" Separator --}}
                @if(is_string($link))
                    <li class="pagination-item">
                        <span class="pagination-dots">{{ $link }}</span>
                    </li>
                @endif

                @if($link['url'])
                <li class="pagination-item">
                    <a href="{{ $link['url'] }}"
                       @if($async) @click.prevent="asyncRequest" @endif
                       class="pagination-link @if($link['active']) _is-active @endif"
                    >
                        {!! $link['label'] !!}
                    </a>
                </li>
                @endif
            @endforeach

            @if ($current_page < $last_page)
                <li class="pagination-item">
                    <a href="{{ $next_page_url }}"
                       @if($async) @click.prevent="asyncRequest" @endif
                       class="pagination-link pagination-link--last"
                       title="{!! $translates['next']  !!}"
                    >
                        <x-moonshine::icon icon="chevron-double-right" />
                    </a>
                </li>
            @endif
        </ul>
        <div class="pagination-results">
            {!! $translates['showing']  !!}
            @if ($from)
                {{ $from }}
                {!! $translates['to']  !!}
                {{ $to }}
            @else
                {{ $per_page }}
            @endif
            {!! $translates['of']  !!}
            {{ $total }}
            {!! $translates['results']  !!}
        </div>
    </div>
    <!-- END: Pagination -->
@endif
