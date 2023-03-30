@php
if($element->isFile()) {
    $value = false;
} elseif(isset($valueKey)) {
    $value = is_array($element->formViewValue($item)) ? ($element->formViewValue($item)[$valueKey] ?? '') : '';
} else {
    $value = (string) $element->formViewValue($item);
}
@endphp

<x-moonshine::form.input
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
        'type' => $element->type(),
        'value' => $value
    ])"
    @class(['form-invalid' => $errors->has($element->name())])
/>
