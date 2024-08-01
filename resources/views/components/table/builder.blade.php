@props([
    'rows',
    'fields',
    'bulkButtons',
    'asyncUrl',
    'async' => false,
    'preview' => false,
    'simple' => false,
    'simplePaginate' => false,
    'editable' => false,
    'notfound' => false,
    'vertical' => false,
    'creatable' => false,
    'reindex' => false,
    'sortable' => false,
    'columnSelection' => false,
    'searchable' => false,
    'searchValue' => '',
    'name' => 'default',
    'sticky' => false
])

<div x-data="tableBuilder(
    {{ (int) $creatable }},
    {{ (int) $sortable }},
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

    @if($columnSelection)
        <div class="flex justify-end">
            <x-moonshine::dropdown>
                <div class="p-2">
                    @foreach($fields as $field)
                        @if($field->isColumnSelection())
                            <div class="form-group form-group-inline">
                                <x-moonshine::form.switcher
                                    :id="'column_selection' . $field->id()"
                                    data-column-selection-checker="true"
                                    data-column="{{ $field->id() }}"
                                    @change="columnSelection()"
                                />

                                <x-moonshine::form.label :for="'column_selection' . $field->id()">
                                    {{ $field->label() }}
                                </x-moonshine::form.label>
                            </div>
                        @endif
                    @endforeach
                </div>

                <x-slot:toggler>
                    <x-moonshine::icon icon="heroicons.outline.table-cells" />
                </x-slot:toggler>
            </x-moonshine::dropdown>
        </div>
    @endif

    <x-moonshine::loader x-show="loading" />
        <div x-show="!loading">
        <x-moonshine::table
                :simple="$simple"
                :sticky="$sticky"
                :notfound="$notfound"
                :attributes="$attributes"
                :creatable="$creatable"
                data-name="{{ $name }}"
        >
            @if(!$vertical)
                <x-slot:thead>
                    <x-moonshine::table.head
                        :rows="$rows"
                        :fields="$fields"
                        :actions="$bulkButtons"
                        :asyncUrl="$asyncUrl"
                        :preview="$preview"
                    />
                </x-slot:thead>
            @endif

            @if($rows->isNotEmpty())
                <x-slot:tbody>
                    <x-moonshine::table.body
                        :rows="$rows"
                        :vertical="$vertical"
                        :preview="$preview"
                        :editable="$editable"
                        :simple="$simple"
                        :sticky="$sticky"
                        :hasActions="$bulkButtons->isNotEmpty()"
                        :has-click-action="$attributes->get('data-click-action') !== null"
                    />
                </x-slot:tbody>
            @endif

            @if(!$preview)
            <x-slot:tfoot
                ::class="actionsOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'"
            >
                <x-moonshine::table.foot
                    :rows="$rows"
                    :actions="$bulkButtons"
                />
            </x-slot:tfoot>
            @endif
        </x-moonshine::table>

        @if($creatable)
            <x-moonshine::divider />

            {!! $createButton !!}
        @endif

        @if(!$preview && $hasPaginator)
            {{ $paginator->links(
                $simplePaginate
                    ? 'moonshine::ui.simple-pagination'
                    : 'moonshine::ui.pagination',
                ['async' => $async]
            ) }}
        @endif
    </div>
</div>
