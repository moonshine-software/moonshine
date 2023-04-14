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
            @include('moonshine::crud.shared.form-hidden', ['resource' => $resource])

            <x-moonshine::form.input
                type="hidden"
                name="ids"
                class="actionsCheckedIds"
                value=""
            />

            <x-moonshine::form.button type="submit" title="{{ $action->label() }}">
                {{ $action->getIcon(6) }} {{ $action->label() }}
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
        @include('moonshine::crud.shared.form-hidden', ['resource' => $resource])

        <x-moonshine::form.input
            type="hidden"
            name="ids"
            class="actionsCheckedIds"
            value=""
        />

        <x-moonshine::form.button type="submit" title="{{ $action->label() }}">
            {{ $action->getIcon(6) }} {{ $action->label() }}
        </x-moonshine::form.button>
    </x-moonshine::form>
@endif
