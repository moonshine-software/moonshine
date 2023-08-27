@if($action->isInOffCanvas())
    <x-moonshine::offcanvas
        title="{{ $action->offCanvas()->title($action) }}"
        :left="$action->offCanvas()->isLeft()"
    >
        <x-slot:toggler :class="$action->attributes()->get('class')">
            <x-moonshine::icon
                :icon="$action->iconValue()"
                size="6"
            />

            {{ $action->label() }}
        </x-slot:toggler>

        {!! $action->offCanvas()->content($action) !!}
    </x-moonshine::offcanvas>
@elseif($action->isInModal())
    <div x-data="{ id: $id('modal') }">
        <x-moonshine::modal :wide="$action->modal()->isAsync()" title="{{ $action->modal()->title($action) }}">
            @if($action->modal()->isAsync())
                <div :id="id">
                    <x-moonshine::loader />
                </div>
            @endif

            <div class="mb-4">
                {!! $action->modal()->content($action) !!}
            </div>

            @if($action->modal()->getButtons()->isNotEmpty())
                <x-moonshine::action-group
                    :actions="$action->modal()->getButtons()"
                />
            @endif

            <x-slot name="outerHtml">
                @if($action->modal()->isAsync())
                    <div x-data="asyncData">
                        <x-moonshine::link
                            :attributes="$action->attributes()"
                            :icon="$action->iconValue()"
                            @click.prevent="toggleModal;load('{!! str_replace('&amp;', '&', $action->url()) !!}', id);"
                        >
                            {{ $action->label() }}
                        </x-moonshine::link>
                    </div>
                @else
                    <x-moonshine::link
                        :attributes="$action->attributes()"
                        :icon="$action->iconValue()"
                        @click.prevent="toggleModa;"
                    >
                        {{ $action->label() }}
                    </x-moonshine::link>
                @endif
            </x-slot>

        </x-moonshine::modal>
    </div>
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

