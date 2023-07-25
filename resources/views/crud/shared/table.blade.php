<div x-data="crudTable({{ $resource->isRelatable() ? 'true' : 'false' }})">
    <x-moonshine::loader x-show="loading" />
    <div x-show="!loading">
        @if($resource->isRelatable())
            <x-moonshine::column class="flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap">
                @if(!empty($resource->queryTags()))
                    <div class="flex items-center gap-2">
                        @foreach($resource->queryTags() as $queryTag)
                            <x-moonshine::link
                                :href="request()->fullUrlWithQuery(['queryTag' => $queryTag->uri()])"
                                :filled="request('queryTag') === $queryTag->uri()"
                                :icon="$queryTag->iconValue()"
                                @click.prevent="canBeAsync"
                            >
                                {{ $queryTag->label() }}
                            </x-moonshine::link>
                        @endforeach
                    </div>
                @endif

                @if($resource->search())
                    <div class="flex items-center gap-2">
                        <form action="{{ $resource->currentRoute() }}"
                              @submit.prevent="canBeAsync"
                        >
                            <x-moonshine::form.input
                                type="hidden"
                                name="_field_relation"
                                :value="request('_field_relation')"
                            />

                            <x-moonshine::form.input
                                name="search"
                                type="search"
                                value="{{ request('search', '') }}"
                                placeholder="{{ trans('moonshine::ui.search') }}"
                            />
                        </form>
                    </div>
                @endif
            </x-moonshine::column>
        @endif

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

            @if($resource->isPaginationUsed() && !$resource->isPreviewMode())
                {{ $resources->links($resource::$simplePaginate ? 'moonshine::ui.simple-pagination' : 'moonshine::ui.pagination') }}
            @endif
        @else
            <x-moonshine::alert type="default" class="my-4" icon="heroicons.no-symbol">
                {{ trans('moonshine::ui.notfound') }}
            </x-moonshine::alert>
        @endif
    </div>
</div>




