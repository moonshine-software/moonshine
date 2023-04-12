<x-moonshine::form.file
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'name' => $element->name(),
    ])"
    :files="is_iterable($element->formViewValue($item)) ? $element->formViewValue($item) : [$element->formViewValue($item)]"
    :removable="$element->isRemovable()"
    :imageable="true"
    :dir="$element->getDir()"
    :path="$element->path('')"
/>
