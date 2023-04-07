@foreach($resource->bulkActions() as $index => $action)
    <x-moonshine::form
        :action="$resource->route('actions.bulk', query: ['index' => $index])"
        :raw="true"
        method="POST"
    >
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

        <x-moonshine::form.button type="submit" title="{{ $action->label() }}">
            {{ $action->getIcon(6) }}
        </x-moonshine::form.button>
    </x-moonshine::form>
@endforeach

@if($resource->can('massDelete') && in_array('delete', $resource->getActiveActions()))
    <x-moonshine::modal title="{{ trans('moonshine::ui.deleting') }}">
        {{ trans('moonshine::ui.confirm_delete') }}

        <x-moonshine::form
            method="POST"
            action="{{ $resource->route('massDelete') }}"
            :raw="true"
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

            <x-moonshine::form.button type="submit" class="btn-pink">
                {{ trans('moonshine::ui.confirm') }}
            </x-moonshine::form.button>
        </x-moonshine::form>

        <x-slot name="outerHtml">
            <x-moonshine::link
                :filled="false"
                icon="heroicons.outline.trash"
                class="btn-pink"
                @click.prevent="toggleModal"
            />
        </x-slot>
    </x-moonshine::modal>
@endif
