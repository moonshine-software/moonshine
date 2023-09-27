<x-moonshine::form.input
    :attributes="$element->attributes()->except('x-on:change')->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
        'value' => (string) $element->value()
    ])"
    :@change="$element->attributes()->get('x-on:change')"
/>
