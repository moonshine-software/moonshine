@props([
    'value' => '',
    'options' => '',
])

<div class="easyMde">
    <x-moonshine::form.textarea
        :attributes="$element->attributes()->merge([
            'id' => 'markdown_' . $element->id(),
            'name' => $element->name()
        ])"
        @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
        x-data="easyMde({{ $options }})"
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>
