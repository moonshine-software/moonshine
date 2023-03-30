<x-moonshine::form.textarea
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'aria-label' => $element->label() ?? '',
        'name' => $element->name(),
    ])"
    @class(['form-invalid' => $errors->has($element->name())])
>{!! $element->formViewValue($item) ?? '' !!}</x-moonshine::form.textarea>
