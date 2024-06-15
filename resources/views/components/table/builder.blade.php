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
    'searchValue' => '',
])
<div x-data="tableBuilder(
    {{ (int) $creatable }},
    {{ (int) $reorderable }},
    {{ (int) $reindex }},
    {{ (int) $async }},
    '{{ $asyncUrl }}'
)"
     data-pushstate="{{ $attributes->get('data-pushstate', false)}}"
    @defineEvent('table-row-added', $name, 'add(true)')
    @defineEvent('table-reindex', $name, 'resolveReindex')
    @defineEventWhen($async, 'table-updated', $name, 'asyncRequest')
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
                    placeholder="{{ __('moonshine::ui.search') }}"
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
            data-name="{{ $name }}"
        >
            @if($headRows->isNotEmpty())
                <x-slot:thead>
                    @foreach($headRows as $row)
                        {!! $row->render() !!}
                    @endforeach
                </x-slot:thead>
            @endif

            @if($rows->isNotEmpty())
                <x-slot:tbody>
                    @foreach($rows as $row)
                        {!! $row->render() !!}
                    @endforeach
                </x-slot:tbody>
            @endif

            @if($footRows->isNotEmpty())
                <x-slot:tfoot>
                    @foreach($footRows as $row)
                        {!! $row->render() !!}
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
