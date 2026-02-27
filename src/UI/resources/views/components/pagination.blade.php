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

@php
    $previousTranslate = html_entity_decode((string) ($translates['previous'] ?? ''));
    $nextTranslate = html_entity_decode((string) ($translates['next'] ?? ''));
    $showingTranslate = html_entity_decode((string) ($translates['showing'] ?? ''));
    $toTranslate = html_entity_decode((string) ($translates['to'] ?? ''));
    $ofTranslate = html_entity_decode((string) ($translates['of'] ?? ''));
    $resultsTranslate = html_entity_decode((string) ($translates['results'] ?? ''));
@endphp

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
                        title="{{ $previousTranslate }}"
                    >
                        {{ $previousTranslate }}
                    </a>
                @else
                    <span class="pagination-link _is-disabled">
                        {{ $previousTranslate }}
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
                        title="{{ $nextTranslate }}"
                    >
                        {{ $nextTranslate }}
                    </a>
                @else
                    <span class="pagination-link _is-disabled">
                        {{ $nextTranslate }}
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
                       title="{{ $previousTranslate }}"
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
                        {{ html_entity_decode((string) ($link['label'] ?? '')) }}
                    </a>
                </li>
                @endif
            @endforeach

            @if ($current_page < $last_page)
                <li class="pagination-item">
                    <a href="{{ $next_page_url }}"
                       @if($async) @click.prevent="asyncRequest" @endif
                       class="pagination-link pagination-link--last"
                       title="{{ $nextTranslate }}"
                    >
                        <x-moonshine::icon icon="chevron-double-right" />
                    </a>
                </li>
            @endif
        </ul>
        <div class="pagination-results">
            {{ $showingTranslate }}
            @if ($from)
                {{ $from }}
                {{ $toTranslate }}
                {{ $to }}
            @else
                {{ $per_page }}
            @endif
            {{ $ofTranslate }}
            {{ $total }}
            {{ $resultsTranslate }}
        </div>
    </div>
    <!-- END: Pagination -->
@endif
