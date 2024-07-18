<x-moonshine::form.slide-range
    :uniqueId="$element->id()"
    :attributes="$element->attributes()"
    :fromAttributes="$element->getFromAttributes()->class([
        'form-invalid' => formErrors(session('errors', false), $element->getFormName())->has($element->getNameDotFrom())
    ])"
    :toAttributes="$element->getToAttributes()->class([
        'form-invalid' => formErrors(session('errors', false), $element->getFormName())->has($element->getNameDotTo())
    ])"
    :fromValue="$value[$element->fromField] ?? $element->min"
    :toValue="$value[$element->toField] ?? $element->max"
    fromName="{{ $element->name($element->fromField) }}"
    toName="{{ $element->name($element->toField) }}"
    fromField="{{ $element->getNameDotFrom() }}"
    toField="{{ $element->getNameDotTo() }}"
/>

@error($element->getNameDotFrom(), $element->getFormName())
    <x-moonshine::form.input-error>
        {{ $message }}
    </x-moonshine::form.input-error>
@enderror

@error($element->getNameDotTo(), $element->getFormName())
    <x-moonshine::form.input-error>
        {{ $message }}
    </x-moonshine::form.input-error>
@enderror
