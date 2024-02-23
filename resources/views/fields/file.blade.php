<x-moonshine::form.file
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'name' => $element->name(),
    ])"
    :files="$element->getFullPathValues()"
    :raw="is_iterable($value) ? $value : [$value]"
    :removable="$element->isRemovable()"
    :removableAttributes="$element->getRemovableAttributes()"
    :imageable="false"
    :download="$element->canDownload()"
    :names="$element->resolveNames()"
    :itemAttributes="$element->resolveItemAttributes()"
/>
