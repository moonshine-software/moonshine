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
    'escapeUi' => false,
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
                        @if(! $escapeUi)
                            title="{!! $translates['previous']  !!}"
                        @else
                            title="{{ $translates['previous']  }}"
                        @endif
                    >
                        @if(! $escapeUi)
                            {!! $translates['previous'] !!}
                        @else
                            {{ $translates['previous'] }}
                        @endif
                    </a>
                @else
                    <span class="pagination-link _is-disabled">
                        @if(! $escapeUi)
                            {!! $translates['previous'] !!}
                        @else
                            {{ $translates['previous'] }}
                        @endif
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
                        @if(! $escapeUi)
                            title="{!! $translates['next']  !!}"
                        @else
                            title="{{ $translates['next']  }}"
                        @endif
                    >
                        @if(! $escapeUi)
                            {!! $translates['next'] !!}
                        @else
                            {{ $translates['next'] }}
                        @endif
                    </a>
                @else
                    <span class="pagination-link _is-disabled">
                        @if(! $escapeUi)
                            {!! $translates['next'] !!}
                        @else
                            {{ $translates['next'] }}
                        @endif
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
                       @if(! $escapeUi)
                           title="{!! $translates['previous']  !!}"
                       @else
                           title="{{ $translates['previous']  }}"
                       @endif
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
                        @if(! $escapeUi)
                            {!! $link['label'] !!}
                        @else
                            {{ $link['label'] }}
                        @endif
                    </a>
                </li>
                @endif
            @endforeach

            @if ($current_page < $last_page)
                <li class="pagination-item">
                    <a href="{{ $next_page_url }}"
                       @if($async) @click.prevent="asyncRequest" @endif
                       class="pagination-link pagination-link--last"
                       @if(! $escapeUi)
                           title="{!! $translates['next']  !!}"
                       @else
                           title="{{ $translates['next']  }}"
                       @endif
                    >
                        <x-moonshine::icon icon="chevron-double-right" />
                    </a>
                </li>
            @endif
        </ul>
        <div class="pagination-results">
            @if(! $escapeUi)
                {!! $translates['showing']  !!}
            @else
                {{ $translates['showing']  }}
            @endif
            @if ($from)
                {{ $from }}
                @if(! $escapeUi)
                    {!! $translates['to']  !!}
                @else
                    {{ $translates['to']  }}
                @endif
                {{ $to }}
            @else
                {{ $per_page }}
            @endif
            @if(! $escapeUi)
                {!! $translates['of']  !!}
            @else
                {{ $translates['of']  }}
            @endif
            {{ $total }}
            @if(! $escapeUi)
                {!! $translates['results']  !!}
            @else
                {{ $translates['results']  }}
            @endif
        </div>
    </div>
    <!-- END: Pagination -->
@endif
