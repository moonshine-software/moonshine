<x-moonshine::grid :attributes="$element->attributes()">
    <x-moonshine::fields-group
        :components="$element->getFields()"
        :container="true"
    />
</x-moonshine::grid>
