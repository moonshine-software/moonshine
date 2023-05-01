<div x-data="crudTable({{ $resource->isRelatable() ? 'true' : 'false' }})">
    <x-moonshine::loader x-show="loading" />
    <div x-show="!loading">
        @if($resources->isNotEmpty())
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

            @if(!$resource->isPreviewMode())
                {{ $resources->links($resource::$simplePaginate ? 'moonshine::ui.simple-pagination' : 'moonshine::ui.pagination') }}
            @endif
        @else
            <x-moonshine::alert type="default" class="my-4" icon="heroicons.no-symbol">
                {{ trans('moonshine::ui.notfound') }}
            </x-moonshine::alert>
        @endif
    </div>
</div>




