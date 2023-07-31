<x-moonshine::form
    :attributes="$attributes"
    x-data="crudForm()"
>
    <x-moonshine::fields-group
        :components="$fields"
    />

    <x-slot:buttons>
        <x-moonshine::form.button
                :attributes="$submitAttributes->merge([
                'class' => 'form_submit_button',
                'type' => 'submit'
            ])"
        >
            <x-moonshine::spinner
                    color="pink"
                    class="form_submit_button_loader"
                    style="display: none;"
            />

            {{ $submitLabel }}
        </x-moonshine::form.button>

        @includeWhen($buttons->isNotEmpty(), 'moonshine::crud.shared.item-actions', [
            'actions' => $buttons,
        ])
    </x-slot:buttons>
</x-moonshine::form>
