<x-moonshine::modal
    :wide="$element->isWide()"
    :title="$element->title()"
    :async="$element->isAsync()"
    :asyncUrl="$element->asyncUrl()"
>
    <div>
        {!! $element->getContent() !!}
    </div>

    <x-slot name="outerHtml">
        <x-moonshine::link
            :attributes="$element->attributes()"
            :icon="$element->iconValue()"
        >
            {{ $element->label() ?? '' }}
        </x-moonshine::link>
    </x-slot>
</x-moonshine::modal>
