<div
    x-data="{
         range_from_{{ $element->id() }}: '{{ $value[$element->fromField] ?? '' }}',
         range_to_{{ $element->id() }}: '{{ $value[$element->toField] ?? '' }}'
     }"
    {{ $element->attributes()
        ->only('class')
        ->merge(['class' => 'form-group form-group-inline']) }}

    data-field-block="{{ $element->name() }}"
>
    <x-moonshine::form.input
        :attributes="$element->getFromAttributes()->merge([
            'name' => $element->name($element->fromField),
        ])"
        @class([
            'form-invalid' => formErrors($errors ?? false, $element->getFormName())->has($element->getNameDotFrom())
        ])
        x-bind:max="range_to_{{ $element->id() }}"
        x-model="range_from_{{ $element->id() }}"
        value="{{ $value[$element->fromField] ?? '' }}"
    />

    <x-moonshine::form.input
        :attributes="$element->getToAttributes()->merge([
            'name' => $element->name($element->toField)
        ])"
        @class([
            'form-invalid' => formErrors($errors ?? false, $element->getFormName())->has($element->getNameDotTo())
        ])
        x-bind:min="range_from_{{ $element->id() }}"
        x-model="range_to_{{ $element->id() }}"
        value="{{ $value[$element->toField] ?? '' }}"
    />
</div>

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
