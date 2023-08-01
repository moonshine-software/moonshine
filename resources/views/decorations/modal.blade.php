@if($element->isAsync())
    <x-moonshine::async-modal
            route="{{ $element->asyncUrl() }}"
            title="{{ $element->title() }}"
            :class="$element->attributes()->get('class')"
    >
        <x-moonshine::icon
                :icon="$element->iconValue()"
                size="6"
        />

        {{ $element->label() ?? '' }}
    </x-moonshine::async-modal>
@else
    <x-moonshine::modal
        :wide="$element->isWide()"
        :title="$element->title()"
    >
        <div>
            {!! $element->getContent() !!}
        </div>

        <x-slot name="outerHtml">
            <x-moonshine::link
                :attributes="$element->attributes()"
                @click.prevent="toggleModal;"
                :icon="$element->iconValue()"
            >
                {{ $element->label() ?? '' }}
            </x-moonshine::link>
        </x-slot>
    </x-moonshine::modal>
@endif