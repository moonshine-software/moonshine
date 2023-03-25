@php
if($field->isFile()) {
    $value = false;
} elseif(isset($valueKey)) {
    $value = is_array($field->formViewValue($item)) ? ($field->formViewValue($item)[$valueKey] ?? '') : '';
} else {
    $value = (string) $field->formViewValue($item);
}
@endphp

<x-moonshine::form.input
    :attributes="$field->attributes()->merge([
        'id' => $field->id(),
        'placeholder' => $field->label() ?? '',
        'name' => $field->name(),
        'type' => $field->type(),
        'value' => $value
    ])"
    @class(['form-invalid' => $errors->has($field->name())])
/>
