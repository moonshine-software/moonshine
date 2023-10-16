<x-moonshine::grid :attributes="$attributes">
    <x-moonshine::fields-group
        :components="$element->getFields()"
        :container="true"
    />
</x-moonshine::grid>
