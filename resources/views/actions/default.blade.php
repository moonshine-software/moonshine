<x-moonshine::link
    :href="$action->url()"
    :filled="true"
    class="dropdown-menu-link w-full"
    :icon="$action->iconValue()"
>
    {{ $action->label() }}
</x-moonshine::link>
