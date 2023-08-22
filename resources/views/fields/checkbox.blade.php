<div x-data>
    <x-moonshine::form.input
        type="hidden"
        name="{{ $element->name() }}"
        :attributes="$element->attributes()->except(['class', 'id', 'type', 'checked', 'value'])"
        value="{{ $element->getOffValue() }}"
    />

    <x-moonshine::form.input
        :attributes="$element->attributes()->merge([
                'id' => $element->id(),
                'name' => $element->name(),
                'value' => $element->getOnValue(),
                'checked' => $element->isChecked()
            ])"
        @class(['form-invalid' => $errors->{$element->getFormName()}->has($element->name())])
        x-bind:checked="{{ $element->attributes()->get('x-model-field') ? $element->attributes()->get('x-model-field') . '==`'.$element->getOnValue().'`' : '$el.checked' }}"
        x-on:change="$el.checked ? $el.value = '{{ $element->getOnValue() }}' : $el.value = '{{ $element->getOffValue() }}'"
    />
</div>
