@props([
    'label' => '',
    'errors' => [],
    'beforeLabel' => false,
])
<x-moonshine::form.input-wrapper
    :attributes="$attributes"
    :label="$label ?? ''"
    :beforeLabel="$beforeLabel ?? false"
    :errors="$errors"
>
    <x-moonshine::form.input
        :attributes="$attributes->except(['class'])"
    />
</x-moonshine::form.input-wrapper>
