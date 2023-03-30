<x-moonshine::grid>
    <x-moonshine::resource-renderable
        :components="$element->getFields()"
        :item="$item"
        :resource="$resource"
        :container="true"
    />
</x-moonshine::grid>
