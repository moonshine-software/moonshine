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
    'searchValue' => '',
    'translates' => [],
])
<div x-data="tableBuilder(
    {{ (int) $creatable }},
    {{ (int) $reorderable }},
    {{ (int) $reindex }},
    {{ (int) $async }},
    '{{ $asyncUrl }}'
)"
     data-pushstate="{{ $attributes->get('data-pushstate', false)}}"
    @defineEvent('table_row_added', $name, 'add(true)')
    @defineEvent('table_reindex', $name, 'resolveReindex')
    @defineEventWhen($async, 'table_updated', $name, 'asyncRequest')
    {{ $attributes->only(['data-events'])}}
>
    @if($async && $searchable)
        <div class="flex items-center gap-2">
            <form action="{{ $asyncUrl }}"
                  @submit.prevent="asyncFormRequest"
            >
                <x-moonshine::form.input
                    name="search"
                    type="search"
                    value="{{ $searchValue }}"
                    placeholder="{{ $translates['search'] }}"
                />
            </form>
        </div>
    @endif

    <x-moonshine::loader x-show="loading" />
    <div x-show="!loading">
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
    </div>
</div>
