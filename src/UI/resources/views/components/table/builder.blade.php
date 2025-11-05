@props([
    'name' => 'default',
    'rows' => [],
    'headRows' => [],
    'footRows' => [],
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
    'searchValue' => '',
    'translates' => [],
    'topLeft' => null,
    'topRight' => null,
    'skeleton' => false,
    'loader' => true,
])
<div
    class="js-table-builder-container"
    @if($async && $lazy) data-lazy="{{ "table_updated:$name" }}" @endif
>
    <div
        class="js-table-builder-wrapper"
        x-data="tableBuilder(
            {{ (int) $creatable }},
            {{ (int) $reorderable }},
            {{ (int) $reindex }},
            {{ (int) $async }},
            '{{ $asyncUrl }}'
        )"
        @defineEvent('table_empty_row_added', $name, 'add(true)')
        @defineEvent('table_reindex', $name, 'resolveReindex')
        @defineEventWhen($async, 'table_updated', $name, 'asyncRequest')
        @defineEventWhen($async, 'table_row_added', $name, 'asyncRowRequest()')
        {{ $attributes }}
    >
        <x-moonshine::iterable-wrapper
            :searchable="$async && $searchable"
            :search-placeholder="$translates['search']"
            :search-value="$searchValue"
            :search-url="$asyncUrl"
            :loader="$loader"
        >
            @if($skeleton)
                <x-slot:skeleton>
                    <x-moonshine::table
                        :simple="$simple"
                        :notfound="false"
                        :translates="$translates"
                        :data-skeleton="true"
                    >
                        @if($headRows->isNotEmpty())
                            <x-slot:thead>
                                @foreach($headRows as $row)
                                    {!! $row !!}
                                @endforeach
                            </x-slot:thead>
                        @endif
                        <x-slot:tbody>
                            @if ($rows->count() > 0)
                                @for($i = 0; $i < $rows->count(); $i++)
                                    <tr>
                                        @foreach($row->getCells() as $column)
                                            <td>
                                                <div class="skeleton"></div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endfor
                            @else
                                <tr>
                                    @foreach($row->getCells() as $column)
                                        <td>
                                            <div class="skeleton"></div>
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($row->getCells() as $column)
                                        <td>
                                            <div class="skeleton"></div>
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($row->getCells() as $column)
                                        <td>
                                            <div class="skeleton"></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        </x-slot:tbody>
                    </x-moonshine::table>
                </x-slot:skeleton>
            @endif

            @if($topLeft ?? false)
                <x-slot:topLeft>
                    {!! $topLeft ?? '' !!}
                </x-slot:topLeft>
            @endif

            @if($topRight ?? false)
                <x-slot:topRight>
                    {!! $topRight ?? '' !!}
                </x-slot:topRight>
            @endif

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
