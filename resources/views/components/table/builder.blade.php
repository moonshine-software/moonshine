<div x-data="crudTable">
    <x-moonshine::table
            :crudMode="true"
            :notfound="true"
            :attributes="$attributes"
    >
        <x-slot:thead>
            <x-moonshine::table.head
                :fields="$fields"
            />
        </x-slot:thead>

        @if($rows->isNotEmpty())
            <x-slot:tbody>
                <x-moonshine::table.body
                    :rows="$rows"
                />
            </x-slot:tbody>
        @endif

        <x-slot:tfoot x-ref="foot" ::class="actionsOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'">
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