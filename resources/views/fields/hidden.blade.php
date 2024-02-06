<x-moonshine::form.input
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'name' => $element->name(),
        'value' => (string) $value
    ])"
/>
