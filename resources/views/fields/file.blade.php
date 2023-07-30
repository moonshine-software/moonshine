<x-moonshine::form.file
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'name' => $element->name(),
    ])"
    :files="is_iterable($element->value()) ? $element->value() : [$element->value()]"
    :removable="$element->isRemovable()"
    :imageable="false"
    :download="$element->canDownload()"
    :dir="$element->getDir()"
    :path="$element->path('')"
/>
