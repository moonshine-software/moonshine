@props([
    'rows',
    'fields',
    'bulkButtons',
    'asyncUrl',
    'async' => false,
    'preview' => false,
    'simple' => false,
    'editable' => false,
    'notfound' => false,
    'vertical' => false,
    'creatable' => false,
    'reindex' => false,
    'sortable' => false,
])

<div x-data="tableBuilder(
{{ (int) $creatable }},
{{ (int) $sortable }},
{{ (int) $reindex }},
{{ (int) $async }}
)">
    @if($async)
        <div class="flex items-center gap-2">
            <form action="{{ $asyncUrl }}"
                  @submit.prevent="asyncRequest"
                  data-name="{{ $name }}"
            >
                <x-moonshine::form.input
                    name="search"
                    type="search"
                    value="{{ request('search', '') }}"
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
                :creatable="$creatable"
                data-name="{{ $name }}"
        >
            @if(!$vertical)
                <x-slot:thead>
                    <x-moonshine::table.head
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
                        :actions="$bulkButtons"
                    />
                </x-slot:tbody>
            @endif

            @if(!$preview)
            <x-slot:tfoot
                x-ref="foot"
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

            <x-moonshine::link
                class="w-full"
                icon="heroicons.plus-circle"
                @click.prevent="add()"
            >
                @lang('moonshine::ui.add')
            </x-moonshine::link>
        @endif

        @if(!$preview && $hasPaginator)
            {{ $paginator->links('moonshine::ui.pagination', ['async' => $async]) }}
        @endif
    </div>
</div>
