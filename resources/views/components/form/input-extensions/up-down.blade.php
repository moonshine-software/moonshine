@props(['extension'])
<button @click.prevent="toggleDown()"
        {{ $attributes->class(['expansion']) }}
        type="button"
>
    <span>
        <x-moonshine::icon
            icon="heroicons.minus-small"
            size="4"
        />
    </span>
</button>

<button @click.prevent="toggleUp()"
        {{ $attributes->class(['expansion']) }}
        type="button"
>
    <span>
        <x-moonshine::icon
            icon="heroicons.plus-small"
            size="4"
        />
    </span>
</button>
