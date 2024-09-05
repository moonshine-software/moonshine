@props([
    'value'
])
<button @click.prevent="toggleEye()"
        {{ $attributes->class(['expansion']) }}
        type="button"
>
    <span x-show="isHidden">
        <x-moonshine::icon
            icon="eye-slash"
            size="4"
        />
    </span>
    <span x-show="!isHidden">
        <x-moonshine::icon
            icon="eye"
            size="4"
        />
    </span>
</button>
