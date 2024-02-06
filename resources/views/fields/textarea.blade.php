<x-moonshine::form.textarea
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'aria-label' => $element->label() ?? '',
        'name' => $element->name(),
    ])"
    @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
>{!! $value ?? '' !!}</x-moonshine::form.textarea>
