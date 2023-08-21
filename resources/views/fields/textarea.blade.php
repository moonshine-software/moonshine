<x-moonshine::form.textarea
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'aria-label' => $element->label() ?? '',
        'name' => $element->name(),
    ])"
    @class(['form-invalid' => $errors->{$element->getForm()}->has($element->name())])
>{!! $element->value() ?? '' !!}</x-moonshine::form.textarea>
