<div>
    @if($element->isGroup())
        @include('moonshine::fields.multi-checkbox', [
            'element' => $element,
            'item' => $item,
            'resource' => $resource
        ])
    @else
        <x-moonshine::form.input
            type="hidden"
            name="{{ $element->name() }}"
            value="{{ $element->getOffValue() }}"
        />

        <x-moonshine::form.input
            :attributes="$element->attributes()->merge([
                'id' => $element->id(),
                'name' => $element->name(),
                'value' => $element->getOnValue()
            ])"
            @class(['form-invalid' => $errors->has($element->name())])
        />
    @endif
</div>
