<x-slot:button class="form_submit_button">
    {{ trans('moonshine::ui.save') }}
</x-slot:button>

@if($item->exists && !request('relatable_mode'))
    <x-slot:buttons>
        @if(collect($resource->formActions())->filter(fn ($action) => $action->inDropdown())->isNotEmpty())
            <x-moonshine::dropdown>
                <x-slot:toggler class="btn">
                    <x-moonshine::icon icon="heroicons.ellipsis-vertical" />
                </x-slot:toggler>

                <ul class="dropdown-menu">
                    @foreach(collect($resource->formActions())->filter(fn ($action) => $action->inDropdown()) as $index => $action)
                        <li class="dropdown-menu-item">
                            @include('moonshine::crud.shared.form-action-item', [
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

        @foreach(collect($resource->formActions())->filter(fn ($action) => !$action->inDropdown()) as $index => $action)
            @if($action->isSee($item))
                @include('moonshine::crud.shared.item-action-item', [
                    'item' => $item,
                    'action' => $action,
                    'resource' => $resource,
                    'index' => $index
                ])
            @endif
        @endforeach
    </x-slot:buttons>
@endif
