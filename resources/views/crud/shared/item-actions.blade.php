@foreach($resource->itemActions() as $index => $action)
    @if($action->isSee($item))
        <x-moonshine::link
            :href="$resource->route('action', $item->getKey(), request()->routeIs('*.query-tag') ? ['index' => $index, 'redirect_back' => 1] : ['index' => $index])"
            :icon="$action->iconValue()"
            :title="$action->label()"
        />
    @endif
@endforeach

@if(!in_array('show', $except, true) && $resource->can('view', $item) && in_array('show', $resource->getActiveActions()))
    <x-moonshine::link
        :href="$resource->route('show', $item->getKey())"
        :filled="false"
        icon="heroicons.eye"
    />
@endif

@if(!in_array('edit', $except, true) && $resource->can('update', $item) && in_array('edit', $resource->getActiveActions()))
    @if($resource->isEditInModal())
        <x-moonshine::async-modal
            id="edit_{{ $item->getTable() }}_modal_{{ $item->getKey() }}"
            route="{{ $resource->route('edit', $item->getKey()) }}"
            title="{{ $resource->title() }}"
            :filled="true"
        >
            <x-moonshine::icon
                icon="heroicons.pencil-square"
                size="4"
            />
        </x-moonshine::async-modal>
    @else
        <x-moonshine::link
            :href="$resource->route('edit', $item->getKey())"
            icon="heroicons.pencil-square"
            :filled="true"
        />
    @endif
@endif

@if(!in_array('delete', $except, true) && $resource->can('delete', $item) && in_array('delete', $resource->getActiveActions()))
    <x-moonshine::modal
        title="{{ trans('moonshine::ui.deleting') }}"
    >
        {{ trans('moonshine::ui.confirm_delete') }}

        <x-moonshine::form
            method="POST"
            action="{{ $resource->route('destroy', $item->getKey()) }}"
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
