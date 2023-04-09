@props(['extension'])
<button @click.prevent="toggleLock()" class="expansion">
    <span x-show="isLock">
        <x-moonshine::icon
            icon="heroicons.lock-closed"
            size="4"
        />
    </span>
    <span x-show="!isLock">
        <x-moonshine::icon
            icon="heroicons.lock-open"
            size="4"
        />
    </span>
</button>
