@props([
    'actions' => []
])
@if(count($actions))
    <div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
        @if($actions->inDropdown()->isNotEmpty())
            <x-moonshine::dropdown>
                <x-slot:toggler class="btn">
                    <x-moonshine::icon icon="ellipsis-vertical" />
                </x-slot:toggler>

                <ul class="dropdown-menu">
                    @foreach($actions->inDropdown() as $index => $action)
                        <li class="dropdown-menu-item">
                            {!! $action !!}
                        </li>
                    @endforeach
                </ul>
            </x-moonshine::dropdown>
        @endif

        @if($actions->inLine()->isNotEmpty())
            @foreach($actions->inLine() as $index => $action)
                {!! $action !!}
            @endforeach
        @endif

        {{ $slot ?? '' }}
    </div>
@endif
