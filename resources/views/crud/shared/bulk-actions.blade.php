@if(collect($resource->bulkActions())->filter(fn ($action) => $action->inDropdown())->isNotEmpty())
<x-moonshine::dropdown>
    <x-slot:toggler class="btn">
        <x-moonshine::icon icon="heroicons.ellipsis-vertical" />
    </x-slot:toggler>

    <ul class="dropdown-menu">
        @foreach(collect($resource->bulkActions())->filter(fn ($action) => $action->inDropdown()) as $index => $action)
            <li class="dropdown-menu-item">
                @include('moonshine::crud.shared.bulk-action-item', [
                    'action' => $action,
                    'resource' => $resource,
                    'index' => $index
                ])
            </li>
        @endforeach
    </ul>
</x-moonshine::dropdown>
@endif

@foreach(collect($resource->bulkActions())->filter(fn ($action) => !$action->inDropdown()) as $index => $action)
   @include('moonshine::crud.shared.bulk-action-item', [
        'action' => $action,
        'resource' => $resource,
        'index' => $index
   ])
@endforeach

@if($resource->can('massDelete') && in_array('delete', $resource->getActiveActions()))
    <x-moonshine::modal title="{{ trans('moonshine::ui.deleting') }}">
        {{ trans('moonshine::ui.confirm_message') }}

        <x-moonshine::form
            method="POST"
            action="{{ $resource->route('massDelete') }}"
            :raw="true"
        >
            @method("delete")

            @include('moonshine::crud.shared.form-hidden', ['resource' => $resource])

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
