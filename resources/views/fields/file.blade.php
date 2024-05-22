@props([
    'value' => '',
    'files' => [],
    'isRemovable' => false,
    'canDownload' => false,
    'removableAttributes' => '',
])
<x-moonshine::form.file
    :attributes="$attributes"
    :files="$files"
    :removable="$isRemovable"
    :removableAttributes="$removableAttributes"
    :imageable="false"
    :download="$canDownload"
/>
