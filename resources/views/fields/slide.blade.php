<x-moonshine::form.slide-range
    :uniqueId="$element->id()"
    :attributes="$element->attributes()"
    :fromValue="$element->value()[$element->fromField] ?? $element->min"
    :toValue="$element->value()[$element->toField] ?? $element->max"
    fromName="{{ $element->name() }}[{{ $element->fromField }}]"
    toName="{{ $element->name() }}[{{ $element->toField }}]"
    fromField="{{ $element->column() }}.{{ $element->fromField }}"
    toField="{{ $element->column() }}.{{ $element->toField }}"
    @class(['form-invalid' => $errors->has($element->name())])
/>
