@if($action->getFilters()->isNotEmpty())
    <x-moonshine::offcanvas
        title="{{ $action->label() ?? trans('moonshine::ui.filters') }}"
        :left="false"
    >
        <x-slot:toggler class="btn-pink w-full">
            <x-moonshine::icon
                :icon="$action->iconValue()"
                size="6"
            />

            {{ $action->label() ?? trans('moonshine::ui.filters') }}

            @if($action->activeCount())
                ({{ $action->activeCount() }})
            @endif
        </x-slot:toggler>

        {{ $action->getForm()->render() }}
    </x-moonshine::offcanvas>
@endif
