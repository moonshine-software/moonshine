@if($action->isConfirmed())
    <x-moonshine::modal title="{{ $action->modal()->title() }}">
        <div class="mb-4">
            {{ $action->modal()->content() }}
        </div>

        <x-moonshine::form
            :action="$action->url()"
            :raw="true"
            method="POST"
        >
            <x-moonshine::form.input
                type="hidden"
                name="ids"
                class="actionsCheckedIds"
                value=""
            />

            <x-moonshine::form.button type="submit" title="{{ $action->label() }}">
                {{ $action->getIcon(6) }} {{ $action->modal()->getConfirmButtonText() }}
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
        :action="$action->url()"
        :raw="true"
        method="POST"
    >
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
