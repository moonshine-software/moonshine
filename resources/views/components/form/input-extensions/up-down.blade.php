@props([
    'value'
])
<div class="expansion-custom flex" x-data="numberUpDown">
    <button @click.prevent="toggleDown()" class="px-2" type="button">
        <span>
            <x-moonshine::icon
                icon="heroicons.minus-small"
                size="4"
            />
        </span>
    </button>

    <button @click.prevent="toggleUp()"  class="px-2" type="button">
        <span>
            <x-moonshine::icon
                icon="heroicons.plus-small"
                size="4"
            />
        </span>
    </button>
</div>
