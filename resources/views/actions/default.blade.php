@if($action->isInModal())
    <x-moonshine::modal title="{{ $action->modal()->title($action) }}">
        <div class="mb-4">
            {!! $action->modal()->content($action) !!}
        </div>

        @if($action->modal()->getButtons()->isNotEmpty())
            <x-moonshine::action-group
                :actions="$action->modal()->getButtons()"
            />
        @endif

        <x-slot name="outerHtml">
            <x-moonshine::link
                :attributes="$action->attributes()"
                :icon="$action->iconValue()"
                @click.prevent="toggleModal"
            >
                {{ $action->label() }}
            </x-moonshine::link>
        </x-slot>

    </x-moonshine::modal>
@else
    <x-dynamic-component
            :attributes="$action->attributes()"
            :component="'moonshine::' . ($action->inDropdown() ? 'link-native' : 'link')"
            @class(['p-2' => $action->inDropdown()])
            :href="$action->url()"
            :icon="$action->iconValue()"
    >
        {{ $action->label() }}
    </x-dynamic-component>
@endif

