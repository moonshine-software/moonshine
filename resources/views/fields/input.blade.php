<x-moonshine::form.input-extensions
    :extensions="$element->getExtensions()"
>
    <x-moonshine::form.input
        :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'name' => $element->name(),
        'value' => (string) $element->value()
    ])"
        @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
    />
</x-moonshine::form.input-extensions>
