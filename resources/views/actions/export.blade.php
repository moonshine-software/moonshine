<x-moonshine::link
    :href="$action->url()"
    :filled="true"
    class="dropdown-menu-link"
    icon="heroicons.squares-2x2"
>
    {{ $action->label() }}
</x-moonshine::link>
