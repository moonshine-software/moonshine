<x-moonshine::modal title="{{ $action->modal()->title() }}">
    <div class="mb-4">
        {!! $action->modal()->content() !!}
    </div>

    <x-moonshine::link
        :href="$action->url()"
        :icon="$action->iconValue()"
    >
        {{ $action->modal()->getConfirmButtonText() }}
    </x-moonshine::link>

    <x-slot name="outerHtml">
        <x-moonshine::link
            :icon="$action->iconValue()"
            @click.prevent="toggleModal"
        >
            {{ $action->label() }}
        </x-moonshine::link>
    </x-slot>

</x-moonshine::modal>
