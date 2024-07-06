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
    <ul class="pagination-list simple">
        {{-- Previous Page Link --}}
        <li>
            @if ($current_page <= 1)
                <span class="pagination-simple disabled">
                    {!! $translates['previous'] !!}
                </span>
            @else
                <a href="{{ $first_page_url }}"
                   @if($async) @click.prevent="asyncRequest" @endif
                   class="pagination-simple"
                >
                    {!! $translates['previous'] !!}
                </a>
            @endif
        </li>

        {{-- Next Page Link --}}
        <li>
            @if ($next_page_url)
                <a href="{{ $next_page_url }}"
                   @if($async) @click.prevent="asyncRequest" @endif
                   class="pagination-simple"
                >
                    {!! $translates['next'] !!}
                </a>
            @else
                <span class="pagination-simple disabled">
                    {!! $translates['next'] !!}
                </span>
            @endif
        </li>
    </ul>
@elseif ($has_pages)
    <!-- Pagination -->
    <div class="pagination">
        <ul class="pagination-list">
            @if ($current_page > 1)
                <li class="pagination-item">
                    <a href="{{ $prev_page_url }}"
                       @if($async) @click.prevent="asyncRequest" @endif
                       class="pagination-first"
                       title="{!! $translates['previous']  !!}"
                    >
                        <x-moonshine::icon
                            icon="chevron-double-left"
                            size="6"
                        />
                    </a>
                </li>
            @endif

            @foreach ($links as $link)
                {{-- "Three Dots" Separator --}}
                @if (is_string($link))
                    <li class="pagination-item">
                        <span class="pagination-dots">{{ $link }}</span>
                    </li>
                @endif

                @if($link['url'])
                <li class="pagination-item">
                    <a href="{{ $link['url'] }}"
                       @if($async) @click.prevent="asyncRequest" @endif
                       class="pagination-page
                       @if ($link['active']) _is-active @endif"
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
                       class="pagination-last"
                       title="{!! $translates['next']  !!}"
                    >
                        <x-moonshine::icon
                            icon="chevron-double-right"
                            size="6"
                        />
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

