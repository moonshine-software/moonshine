@props([
    'rows',
    'fields',
    'bulkButtons',
    'preview' => false,
    'notfound' => false,
    'vertical' => false,
])
<div>
    <x-moonshine::table
            :classic="!$preview"
            :notfound="$notfound"
            :attributes="$attributes"
    >
        @if(!$vertical)
            <x-slot:thead>
                <x-moonshine::table.head
                    :fields="$fields"
                    :actions="$bulkButtons"
                />
            </x-slot:thead>
        @endif

        @if($rows->isNotEmpty())
            <x-slot:tbody>
                <x-moonshine::table.body
                    :rows="$rows"
                    :vertical="$vertical"
                    :actions="$bulkButtons"
                />
            </x-slot:tbody>
        @endif

        <x-slot:tfoot
            x-ref="foot"
            ::class="actionsOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'"
        >
            <x-moonshine::table.foot
                :rows="$rows"
                :actions="$bulkButtons"
            />
        </x-slot:tfoot>
    </x-moonshine::table>

    @if($hasPaginator)
        {{ $paginator->links('moonshine::ui.pagination') }}
    @endif
</div>
