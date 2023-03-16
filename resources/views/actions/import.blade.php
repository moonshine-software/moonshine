<x-moonshine::offcanvas
    title="{{ $action->label() }}"
    :left="true"
>
    <x-slot:toggler class="btn-pink">
        <x-moonshine::icon icon="heroicons.paper-clip" :size="6" />
        {{ $action->label()  }}
    </x-slot:toggler>

    <x-moonshine::form
        action="{{ $action->url() }}"
        enctype="multipart/form-data"
        method="POST"
    >
        <div class="form-flex-col">
            <x-moonshine::form.input
                type="hidden"
                name="{{ $action->triggerKey }}"
                value="1"
            />

            <x-moonshine::form.input-wrapper
                name="{{ $action->inputName }}"
                label=""
                required
            >
                <x-moonshine::form.input
                    type="file"
                    name="{{ $action->inputName }}"
                    required
                />
            </x-moonshine::form.input-wrapper>
        </div>

        <x-slot:button type="submit">
            {{ trans('moonshine::ui.confirm') }}
        </x-slot:button>
    </x-moonshine::form>
</x-moonshine::offcanvas>
