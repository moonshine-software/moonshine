<x-moonshine::offcanvas
    title="{{ $action->label() }}"
    :left="true"
>
    <x-slot:toggler class="btn-pink w-full">
        <x-moonshine::icon
            :icon="$action->iconValue()"
            :size="6"
        />

        {{ $action->label()  }}
    </x-slot:toggler>

    {{ $action->getForm()->render() }}
</x-moonshine::offcanvas>
