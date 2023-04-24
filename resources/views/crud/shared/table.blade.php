<x-moonshine::table
    :crudMode="true"
>
    <x-slot:thead>
        @include("moonshine::crud.shared.table-head", [$resource])
    </x-slot:thead>
    <x-slot:tbody>
        @include("moonshine::crud.shared.table-body", [$resources])
    </x-slot:tbody>

    <x-slot:tfoot x-ref="foot" ::class="actionsOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'">
        @includeWhen(!$resource->isPreviewMode(), "moonshine::crud.shared.table-foot", [$resource])
    </x-slot:tfoot>
</x-moonshine::table>




