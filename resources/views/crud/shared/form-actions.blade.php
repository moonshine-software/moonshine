<x-slot:button class="form_submit_button">
    <x-moonshine::spinner
        color="pink"
        class="form_submit_button_loader"
        style="display: none;"
    />

    {{ trans('moonshine::ui.save') }}
</x-slot:button>

@if($item->exists)
    <x-slot:buttons>
        @if($resource->formActionsCollection()->onlyVisible($item)->inDropdown()->isNotEmpty())
            <x-moonshine::dropdown>
                <x-slot:toggler class="btn">
                    <x-moonshine::icon icon="heroicons.ellipsis-vertical" />
                </x-slot:toggler>

                <ul class="dropdown-menu">
                    @foreach($resource->formActionsCollection()->onlyVisible($item)->inDropdown() as $index => $action)
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

        @foreach($resource->formActionsCollection()->onlyVisible($item)->inLine() as $index => $action)
            @include('moonshine::crud.shared.form-action-item', [
                'item' => $item,
                'action' => $action,
                'resource' => $resource,
                'index' => $index
            ])
        @endforeach
    </x-slot:buttons>
@endif
