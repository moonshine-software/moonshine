@php
if($element->isFile()) {
    $value = false;
} elseif(isset($valueKey)) {
    $value = is_array($element->value()) ? ($element->value()[$valueKey] ?? '') : '';
} else {
    $value = (string) $element->value();
}
@endphp

<x-moonshine::form.input-extensions
    :extensions="method_exists($element, 'getExtensions') ? $element->getExtensions() : null"
>
    <x-moonshine::form.input
        :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
        'value' => $value
    ])"
        @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
    />
</x-moonshine::form.input-extensions>
