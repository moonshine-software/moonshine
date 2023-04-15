@props(['extension'])
<button @click.prevent="toggleEye()" class="expansion" type="button">
    <span x-show="isHidden">
        <x-moonshine::icon
            icon="heroicons.outline.eye-slash"
            size="4"
        />
    </span>
    <span x-show="!isHidden">
        <x-moonshine::icon
            icon="heroicons.outline.eye"
            size="4"
        />
    </span>
</button>
