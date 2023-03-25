@if($resource->hasMassAction())
    <td class="text-center"
        colspan="{{ count($resource->getFields()->indexFields())+2 }}"
    >
        <div class="flex items-center gap-2">
            @foreach($resource->bulkActions() as $index => $action)
                <x-moonshine::form :action="$resource->route('actions.bulk', query: ['index' => $index])" method="POST">
                    @if($resource->isRelatable())
                        <x-moonshine::form.input
                            type="hidden"
                            name="relatable_mode"
                            value="1"
                        />
                    @endif

                    @if(request()->routeIs('*.query-tag'))
                        <x-moonshine::form.input
                            type="hidden"
                            name="redirect_back"
                            value="1"
                        />
                    @endif

                    <x-moonshine::form.input
                        type="hidden"
                        name="ids"
                        class="actionsCheckedIds"
                        value=""
                    />

                    <x-slot:button type="submit" title="{{ $action->label() }}">
                        {{ $action->getIcon(6) }}
                    </x-slot:button>
                </x-moonshine::form>
            @endforeach

            @if($resource->can('massDelete') && in_array('delete', $resource->getActiveActions()))
                <x-moonshine::modal title="{{ trans('moonshine::ui.deleting') }}">
                    {{ trans('moonshine::ui.confirm_delete') }}

                    <x-moonshine::form
                        method="POST"
                        action="{{ $resource->route('massDelete') }}"
                    >
                        @method("delete")

                        @if($resource->isRelatable())
                            <x-moonshine::form.input
                                type="hidden"
                                name="relatable_mode"
                                value="1"
                            />
                        @endif

                        @if(request()->routeIs('*.query-tag'))
                            <x-moonshine::form.input
                                type="hidden"
                                name="redirect_back"
                                value="1"
                            />
                        @endif

                        <x-moonshine::form.input
                            type="hidden"
                            name="ids"
                            class="actionsCheckedIds"
                            value=""
                        />

                        <x-slot:button type="submit" class="btn-pink">
                            {{ trans('moonshine::ui.confirm') }}
                        </x-slot:button>
                    </x-moonshine::form>

                    <x-slot name="outerHtml">
                        <x-moonshine::link
                            :filled="false"
                            icon="heroicons.trash"
                            class="btn-pink"
                            @click.prevent="toggleModal"
                        />
                    </x-slot>
                </x-moonshine::modal>
            @endif
        </div>
    </td>
@endif
