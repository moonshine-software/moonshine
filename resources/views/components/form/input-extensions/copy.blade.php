@props(['extension'])
<button @click.prevent="copy()" class="expansion">
    <x-moonshine::icon
        icon="heroicons.outline.clipboard"
        size="4"
    />
</button>
