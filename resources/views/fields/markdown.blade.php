<div class="easyMde">
    <x-moonshine::form.textarea
        :attributes="$element->attributes()->merge([
            'id' => 'markdown_' . $element->id(),
            'name' => $element->name(),
            'x-data' => 'easyMde'
        ])"
        @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>
