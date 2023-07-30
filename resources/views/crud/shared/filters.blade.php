@if($filters->isNotEmpty())
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
        <x-moonshine::form action="{{ $resource->currentRoute() }}" method="get">
            @if(request('order.field'))
                <x-moonshine::form.input type="hidden" name="order[type]" value="{{ request('order.type') }}" />
                <x-moonshine::form.input type="hidden" name="order[field]" value="{{ request('order.field') }}" />
            @endif

            <div class="form-flex-col">
                <x-moonshine::fields-group
                    :components="$filters"
                />
            </div>

            <x-slot:button type="submit">
                {{ trans('moonshine::ui.search') }}
            </x-slot:button>

            <x-slot:buttons>
                @if(request('filters'))
                    <x-moonshine::link href="{{ $resource->currentRoute(query: ['reset' => true]) }}">
                        {{ trans('moonshine::ui.reset') }}
                    </x-moonshine::link>
                @endif
            </x-slot:buttons>
        </x-moonshine::form>
    </x-moonshine::offcanvas>
@endif
