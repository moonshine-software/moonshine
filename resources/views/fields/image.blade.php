@props([
    'value' => '',
    'fullPathValues' => [],
    'isRemovable' => false,
    'canDownload' => false,
    'removableAttributes' => '',
    'names' => static fn() => '',
    'itemAttributes' => static fn() => '',
])
<x-moonshine::form.file
    :attributes="$attributes"
    :files="$fullPathValues"
    :raw="is_iterable($value) ? $value : [$value]"
    :removable="$isRemovable"
    :removableAttributes="$removableAttributes"
    :imageable="true"
    :names="$names"
    :itemAttributes="$itemAttributes"
/>
