@if($action->isInOffCanvas())
    <x-moonshine::offcanvas
        title="{{ $action->offCanvas()->title($action->getItem()) }}"
        :left="$action->offCanvas()->isLeft()"
        :eventName="$action->offCanvas()->getName()"
    >
        <x-slot:toggler :class="$attributes->get('class')">
            <x-moonshine::icon
                :icon="$action->iconValue()"
                size="6"
            />

            {{ $action->label() }}

            @if($action->hasBadge())
                <x-moonshine::badge color="">{{ $action->getBadge() }}</x-moonshine::badge>
            @endif
        </x-slot:toggler>

        {!! $action->offCanvas()->content($action->getItem()) !!}
    </x-moonshine::offcanvas>
@elseif($action->isInModal())
    <x-moonshine::modal
        :eventName="$action->modal()->getName()"
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
                :attributes="$attributes->merge([
                    '@click.prevent' => 'toggleModal',
                ])"
                :icon="$action->iconValue()"
                :badge="$action->getBadge()"
                :href="$action->url()"
            >
                {{ $action->label() }}
            </x-moonshine::link-button>
        </x-slot>

    </x-moonshine::modal>
@else
    <x-moonshine::link-button
        :attributes="$attributes"
        :href="$action->url()"
        :icon="$action->iconValue()"
        :badge="$action->getBadge()"
    >
        {{ $action->label() }}
    </x-moonshine::link-button>
@endif

