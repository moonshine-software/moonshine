<x-moonshine::form.range
    :uniqueId="$field->id()"
    :attributes="$field->attributes()"
    :fromValue="$field->formViewValue($item)[$field->fromField] ?? $field->min"
    :toValue="$field->formViewValue($item)[$field->toField] ?? $field->max"
    fromName="{{ $field->name() }}[{{ $field->fromField }}]"
    toName="{{ $field->name() }}[{{ $field->toField }}]"
/>
