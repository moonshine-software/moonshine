<x-moonshine::offcanvas
    :title="$element->title()"
    :left="$element->isLeft()"
>
    <x-slot:toggler :class="$element->attributes()->get('class')">
        <x-moonshine::icon
            :icon="$element->iconValue()"
            size="6"
        />

        {{ $element->label() ?? '' }}
    </x-slot:toggler>

    {!! $element->getContent() !!}
</x-moonshine::offcanvas>