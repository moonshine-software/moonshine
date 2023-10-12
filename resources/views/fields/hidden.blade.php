<x-moonshine::form.input
    :attributes="$element->attributes()->except('x-on:change')->merge([
        'id' => $element->id(),
        'name' => $element->name(),
        'value' => (string) $element->value()
    ])"
    :@change="$element->attributes()->get('x-on:change')"
/>
