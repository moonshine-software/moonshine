<x-moonshine::form.file
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'name' => $element->name(),
    ])"
    :files="is_iterable($element->formViewValue($item)) ? $element->formViewValue($item) : [$element->formViewValue($item)]"
    :removable="$element->isRemovable()"
    :imageable="false"
    :download="$element->canDownload()"
    :dir="$element->getDir()"
    :path="$element->path('')"
/>
