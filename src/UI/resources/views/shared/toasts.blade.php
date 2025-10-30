<div x-data="toasts()"
     class="toast-container"
     @toast.window="add($event.detail)"
>
    <template x-for="toast of toasts" :key="toast.id">
        <div
            x-show="visible.includes(toast)"
            x-transition:enter="transition ease-in duration-300"
            x-transition:enter-start="transform opacity-0"
            x-transition:enter-end="transform opacity-100"
            x-transition:leave="transition ease duration-300"
            x-transition:leave-start="transform scale-100 opacity-100"
            x-transition:leave-end="transform scale-90 opacity-0"
            @click="remove(toast.id)"
            class="toast-item"
            :class="{
                    'toast-primary': toast.type === 'primary',
                    'toast-secondary': toast.type === 'secondary',
                    'toast-success': toast.type === 'success',
                    'toast-info': toast.type === 'info',
                    'toast-warning': toast.type === 'warning',
                    'toast-error': toast.type === 'error',
                }"
        >
            <template x-if="toast.type === 'success'">
                <x-moonshine::icon icon="check-circle" />
            </template>
            <template x-if="toast.type === 'error'">
                <x-moonshine::icon icon="x-circle" />
            </template>
            <template x-if="toast.type === 'warning'">
                <x-moonshine::icon icon="exclamation-triangle" />
            </template>
            <template x-if="toast.type === 'info'">
                <x-moonshine::icon icon="information-circle" />
            </template>
            <template x-if="toast.type === 'primary'">
                <x-moonshine::icon icon="bell" />
            </template>
            <template x-if="toast.type === 'secondary'">
                <x-moonshine::icon icon="bell" />
            </template>
            <p x-text="toast.text"></p>
        </div>
    </template>
</div>
