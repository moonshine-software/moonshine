<x-moonshine::form.input
    :attributes="$element->attributes()->merge([
        'id' => empty($element->id()) ? null : $element->id(),
        'name' => $element->name(),
        'value' => (string) $value
    ])"
/>
