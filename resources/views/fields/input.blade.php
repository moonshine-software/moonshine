<x-moonshine::form.input-extensions
    :extensions="$element->getExtensions()"
>
    <x-moonshine::form.input
        :attributes="$element->attributes()->except('x-on:change')->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
        'value' => (string) $element->value()
    ])"
        @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
        :@change="(($updateOnPreview ?? false)
            ? 'updateColumn(
                `'.$element->getUpdateOnPreviewUrl().'`,
                `'.$element->column().'`
            )'
            : $element->attributes()->get('x-on:change')
        )"
    />
</x-moonshine::form.input-extensions>
