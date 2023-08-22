<div x-data="{ color: '{!! (string) $element->value() ?? "#000000" !!}' }">
    <div class="flex flex-row justify-start items-center">
        <div class="relative -mr-10 ml-2 w-8 h-8 rounded-full overflow-hidden">
            <x-moonshine::form.input
                :attributes="$element->attributes()->except('type')->merge([
                    'type' => 'color',
                ])"
                class="absolute -top-2 -left-2 w-16 h-16 rounded-full"
                x-model:value="color"
            />
        </div>

        <x-moonshine::form.input
            :attributes="$element->attributes()->merge([
                'id' => $element->id(),
                'name' => $element->name(),
                'type' => 'text',
                'placeholder' => '#000000',
            ])"
            style="padding-left: 50px;"
            x-model:value="color"
            @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
        />
    </div>
</div>
