@if($action->confirmation())
    <x-moonshine::modal title="{{ trans('moonshine::ui.confirm') }}">
        <div class="mb-4">
            {{ trans('moonshine::ui.confirm_message') }}
        </div>

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

        <x-slot name="outerHtml">
            <x-moonshine::link
                :icon="$action->iconValue()"
                @click.prevent="toggleModal"
            >
                {{ $action->label() }}
            </x-moonshine::link>
        </x-slot>

    </x-moonshine::modal>
@else
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
@endif
