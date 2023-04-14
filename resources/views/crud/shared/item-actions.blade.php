@if($resource->itemActionsCollection()->onlyVisible($item)->inDropdown()->isNotEmpty())
    <x-moonshine::dropdown>
        <x-slot:toggler class="btn">
            <x-moonshine::icon icon="heroicons.ellipsis-vertical" />
        </x-slot:toggler>

        <ul class="dropdown-menu">
            @foreach($resource->itemActionsCollection()->onlyVisible($item)->inDropdown() as $index => $action)
                <li class="dropdown-menu-item">
                    @include('moonshine::crud.shared.item-action-item', [
                        'item' => $item,
                        'action' => $action,
                        'resource' => $resource,
                        'index' => $index
                    ])
                </li>
            @endforeach
        </ul>
    </x-moonshine::dropdown>
@endif

@foreach($resource->itemActionsCollection()->onlyVisible($item)->inLine()  as $index => $action)
    @include('moonshine::crud.shared.item-action-item', [
        'item' => $item,
        'action' => $action,
        'resource' => $resource,
        'index' => $index
    ])
@endforeach

@if(!in_array('show', $except, true) && $resource->can('view', $item) && in_array('show', $resource->getActiveActions()))
    <x-moonshine::link
        :href="$resource->route('show', $item->getKey())"
        :filled="false"
        icon="heroicons.outline.eye"
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
                icon="heroicons.outline.pencil-square"
                size="4"
            />
        </x-moonshine::async-modal>
    @else
        <x-moonshine::link
            :href="$resource->route('edit', $item->getKey())"
            icon="heroicons.outline.pencil-square"
            :filled="true"
        />
    @endif
@endif

@if(!in_array('delete', $except, true) && $resource->can('delete', $item) && in_array('delete', $resource->getActiveActions()))
    <x-moonshine::modal
        title="{{ trans('moonshine::ui.deleting') }}"
    >
        {{ trans('moonshine::ui.confirm_message') }}

        <x-moonshine::form
            method="POST"
            action="{{ $resource->route('destroy', $item->getKey()) }}"
        >
            @method("delete")

            @include('moonshine::crud.shared.form-hidden', ['resource' => $resource])

            <x-slot:button type="submit" class="btn-pink">
                {{ trans('moonshine::ui.confirm') }}
            </x-slot:button>
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
