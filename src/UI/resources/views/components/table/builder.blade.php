@props([
    'name' => 'default',
    'rows' => [],
    'headRows' => [],
    'footRows' => [],
    'columns' => [],
    'headAttributes',
    'bodyAttributes',
    'footAttributes',
    'asyncUrl',
    'async' => false,
    'simple' => false,
    'notfound' => false,
    'creatable' => false,
    'reindex' => false,
    'reorderable' => false,
    'searchable' => false,
    'sticky' => false,
    'lazy' => false,
    'columnSelection' => false,
    'searchValue' => '',
    'translates' => [],
    'topLeft' => null,
    'topRight' => null,
])
<div
    class="js-table-builder-container"
    @if($async && $lazy) data-lazy="{{ "table_updated:$name" }}" @endif
>
    <div x-data="tableBuilder(
    {{ (int) $creatable }},
    {{ (int) $reorderable }},
    {{ (int) $reindex }},
    {{ (int) $async }},
    '{{ $asyncUrl }}'
)"
        @defineEvent('table_row_added', $name, 'add(true)')
        @defineEvent('table_reindex', $name, 'resolveReindex')
        @defineEventWhen($async, 'table_updated', $name, 'asyncRequest')
        {{ $attributes }}
    >
        <x-moonshine::iterable-wrapper
            :searchable="$async && $searchable"
            :search-placeholder="$translates['search']"
            :search-value="$searchValue"
            :search-url="$asyncUrl"
        >
            <x-slot:topLeft>
                {!! $topLeft ?? '' !!}
            </x-slot:topLeft>

            <x-slot:topRight>
                {!! $topRight ?? '' !!}
            </x-slot:topRight>

            <x-moonshine::table
                :simple="$simple"
                :notfound="$notfound"
                :attributes="$attributes"
                :headAttributes="$headAttributes"
                :bodyAttributes="$bodyAttributes"
                :footAttributes="$footAttributes"
                :creatable="$creatable"
                :sticky="$sticky"
                :translates="$translates"
                data-name="{{ $name }}"
            >
                @if($headRows->isNotEmpty())
                    <x-slot:thead>
                        @foreach($headRows as $row)
                            {!! $row !!}
                        @endforeach
                    </x-slot:thead>
                @endif

                @if($rows->isNotEmpty())
                    <x-slot:tbody>
                        @foreach($rows as $row)
                            {!! $row !!}
                        @endforeach
                    </x-slot:tbody>
                @endif

                @if($footRows->isNotEmpty())
                    <x-slot:tfoot>
                        @foreach($footRows as $row)
                            {!! $row !!}
                        @endforeach
                    </x-slot:tfoot>
                @endif
            </x-moonshine::table>

            @if($creatable)
                <x-moonshine::layout.divider />

                {!! $createButton !!}
            @endif

            @if($hasPaginator)
                {!! $paginator !!}
            @endif
        </x-moonshine::iterable-wrapper>
    </div>
</div>
