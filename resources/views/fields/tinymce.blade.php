@props([
    'config' => '',
])
<div class="tinymce">
    <x-moonshine::form.textarea
        :attributes="$element->attributes()->merge([
            'name' => $element->name()
        ])->except('x-bind:id')"
        @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
        ::id="$id('tiny-mce')"
        x-data="tinymce({{ $config }})"
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>
