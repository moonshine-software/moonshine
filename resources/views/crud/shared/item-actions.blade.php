@if($actions->inDropdown()->isNotEmpty())
    <x-moonshine::dropdown>
        <x-slot:toggler class="btn">
            <x-moonshine::icon icon="heroicons.ellipsis-vertical" />
        </x-slot:toggler>

        <ul class="dropdown-menu">
            @foreach($actions->inDropdown() as $index => $action)
                <li class="dropdown-menu-item">
                    @include('moonshine::crud.shared.item-action-item', [
                        'action' => $action,
                        'index' => $index
                    ])
                </li>
            @endforeach
        </ul>
    </x-moonshine::dropdown>
@endif

@if($actions->inLine()->isNotEmpty())
    @foreach($actions->inLine()  as $index => $action)
        @include('moonshine::crud.shared.item-action-item', [
            'action' => $action,
            'index' => $index
        ])
    @endforeach
@endif


