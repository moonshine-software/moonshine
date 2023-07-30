<div x-data="crudTable">
    <x-moonshine::table
            :crudMode="true"
            :attributes="$attributes"
    >
        <x-slot:thead>
            @include('moonshine::crud.shared.table-head', ['fields' => $fields])
        </x-slot:thead>

        <x-slot:tbody>
            @include('moonshine::crud.shared.table-body', ['rows' => $rows])
        </x-slot:tbody>

        <x-slot:tfoot x-ref="foot" ::class="actionsOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'">
            @include('moonshine::crud.shared.table-foot', [
                'rows' => $rows,
                'actions' => $bulkButtons
            ])
        </x-slot:tfoot>
    </x-moonshine::table>

    @if($hasPaginator)
        {{ $paginator->links('moonshine::ui.pagination') }}
    @endif
</div>