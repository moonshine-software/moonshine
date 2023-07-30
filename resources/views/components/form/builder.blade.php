<x-moonshine::form
    :attributes="$attributes"
    x-data="crudForm()"
>
    <x-moonshine::fields-group
        :components="$fields"
    />

    <x-slot:button class="form_submit_button">
        <x-moonshine::spinner
            color="pink"
            class="form_submit_button_loader"
            style="display: none;"
        />

        {{ $submitLabel }}
    </x-slot:button>

    @if($buttons->isNotEmpty())
        <x-slot:buttons>
            @include('moonshine::crud.shared.item-actions', [
                'actions' => $buttons,
            ])
        </x-slot:buttons>
    @endif

</x-moonshine::form>
