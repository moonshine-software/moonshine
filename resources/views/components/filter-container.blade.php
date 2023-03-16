@props([
    'resource',
    'filter',
])
<x-moonshine::form.input-wrapper
    :attributes="$attributes"
    :label="$filter->label()"
    :name="$filter->name()"
>
    {{ $slot }}
</x-moonshine::form.input-wrapper>
