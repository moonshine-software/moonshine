<x-moonshine::form.slide-range
    :uniqueId="$element->id()"
    :attributes="$element->attributes()"
    :fromAttributes="$element->getFromAttributes()"
    :toAttributes="$element->getToAttributes()"
    :fromValue="$value[$element->fromField] ?? $element->min"
    :toValue="$value[$element->toField] ?? $element->max"
    fromName="{{ $element->name($element->fromField) }}"
    toName="{{ $element->name($element->toField) }}"
    fromField="{{ $element->column() }}.{{ $element->fromField }}"
    toField="{{ $element->column() }}.{{ $element->toField }}"
    @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
/>
