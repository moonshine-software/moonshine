<x-moonshine::offcanvas
    title="{{ $action->label() }}"
    :left="true"
>
    <x-slot:toggler class="btn-pink w-full">
        <x-moonshine::icon :icon="$action->iconValue()" :size="6" />
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
                name="{{ $action->getTriggerKey() }}"
                value="1"
            />

            <x-moonshine::form.input-wrapper
                name="{{ $action->inputName }}"
                label=""
                required
            >
                <x-moonshine::form.file
                    name="{{ $action->inputName }}"
                    required
                />
            </x-moonshine::form.input-wrapper>
        </div>

        <x-slot:buttons>
            <x-moonshine::form.button type="submit">
                {{ trans('moonshine::ui.confirm') }}
            </x-moonshine::form.button>
        </x-slot:buttons>
    </x-moonshine::form>
</x-moonshine::offcanvas>
