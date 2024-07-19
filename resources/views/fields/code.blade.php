<div
    @class(['code-container', 'form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
>
    <div
        x-data="code({
        lineNumbers: {{ $element->lineNumbers ? 'true' : 'false' }},
        language: '{{ $element->language ?? 'js' }}',
        readonly: {{ $element->isReadonly() ? 'true' : 'false' }},
    })"
        {{ $element->attributes()
            ->only('class')
            ->merge(['class' => 'w-100 min-h-[300px] relative']) }}
    ></div>

    <x-moonshine::form.textarea
        style="display: none;"
        :attributes="$element->attributes()->merge([
        'aria-label' => $element->label() ?? '',
        'name' => $element->name(),
        'class' => 'code-source'
    ])"
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>

