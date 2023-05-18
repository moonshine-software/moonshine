@props(['extension'])
<button @click.prevent="copy()"
        {{ $attributes->class(['expansion']) }}
        type="button"
>
    <x-moonshine::icon
        icon="heroicons.outline.clipboard"
        size="4"
    />
</button>
