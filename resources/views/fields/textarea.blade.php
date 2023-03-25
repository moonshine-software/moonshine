<x-moonshine::form.textarea
    :attributes="$field->attributes()->merge([
        'id' => $field->id(),
        'placeholder' => $field->label() ?? '',
        'aria-label' => $field->label() ?? '',
        'name' => $field->name(),
    ])"
    @class(['form-invalid' => $errors->has($field->name())])
>
    {!! $field->formViewValue($item) ?? '' !!}
</x-moonshine::form.textarea>
