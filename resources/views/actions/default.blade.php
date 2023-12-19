@if($action->isInOffCanvas())
    <x-moonshine::offcanvas
        title="{{ $action->offCanvas()->title($action->getItem()) }}"
        :left="$action->offCanvas()->isLeft()"
    >
        <x-slot:toggler :class="$attributes->get('class')">
            <x-moonshine::icon
                :icon="$action->iconValue()"
                size="6"
            />

            {{ $action->label() }}
        </x-slot:toggler>

        {!! $action->offCanvas()->content($action->getItem()) !!}
    </x-moonshine::offcanvas>
@elseif($action->isInModal())
    <x-moonshine::modal
        :async="$action->modal()->isAsync()"
        :auto="$action->modal()->isAuto()"
        :autoClose="$action->modal()->isAutoClose()"
        :wide="$action->modal()->isWide()"
        :attributes="$action->modal()->attributes()"
        :closeOutside="$action->modal()->isCloseOutside()"
        :asyncUrl="$action->modal()->isAsync() ? $action->url() : ''"
        title="{{ $action->modal()->title($action->getItem()) }}"
    >
        <div class="mb-4">
            {!! $action->modal()->content($action->getItem()) !!}
        </div>

        @if($action->modal()->getButtons()->isNotEmpty())
            <x-moonshine::action-group
                :actions="$action->modal()->getButtons($action->getItem())"
            />
        @endif

        <x-slot name="outerHtml">
            <x-moonshine::link-button
                :attributes="$attributes"
                :icon="$action->iconValue()"
                :href="$action->url()"
            >
                {{ $action->label() }}
            </x-moonshine::link-button>
        </x-slot>

    </x-moonshine::modal>
@else
    @if($action->inDropdown())
        <x-moonshine::link-native
            :attributes="$attributes"
            @class(['p-2' => $action->inDropdown()])
            :href="$action->url()"
            :icon="$action->iconValue()"
        >
            {{ $action->label() }}
        </x-moonshine::link-native>
    @else
        <x-moonshine::link-button
            :attributes="$attributes"
            @class(['p-2' => $action->inDropdown()])
            :href="$action->url()"
            :icon="$action->iconValue()"
        >
            {{ $action->label() }}
        </x-moonshine::link-button>
    @endif
@endif

