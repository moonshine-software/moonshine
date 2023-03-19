<x-moonshine::grid>
    <x-moonshine::resource-renderable
        :components="$decoration->fields()"
        :item="$item"
        :resource="$resource"
        :container="true"
    />
</x-moonshine::grid>
