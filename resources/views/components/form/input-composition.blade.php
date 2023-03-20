<x-moonshine::form.input-wrapper
    :attributes="$attributes"
    :label="$label ?? ''"
    :beforeLabel="$beforeLabel ?? false"
>
    <x-moonshine::form.input
        :attributes="$attributes->except(['class'])"
    />
</x-moonshine::form.input-wrapper>
