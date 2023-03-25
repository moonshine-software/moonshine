<x-moonshine::grid>
    <x-moonshine::resource-renderable
        :components="$decoration->getFields()"
        :item="$item"
        :resource="$resource"
        :container="true"
    />
</x-moonshine::grid>
