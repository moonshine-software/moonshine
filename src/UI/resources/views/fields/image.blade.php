@props([
    'value' => '',
    'files' => [],
    'isRemovable' => false,
    'canDownload' => false,
    'removableAttributes' => null,
    'hiddenAttributes' => null,
])
<x-moonshine::form.file
    :attributes="$attributes"
    :files="$files"
    :removable="$isRemovable"
    :removableAttributes="$removableAttributes"
    :hiddenAttributes="$hiddenAttributes"
    :imageable="true"
/>
