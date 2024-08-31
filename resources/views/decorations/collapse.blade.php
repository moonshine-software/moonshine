<x-moonshine::collapse :open="$element->isOpen()"
                       :title="$element->label()"
                       :icon="$element->iconValue()"
                       :persist="$element->isPersist()"
                       :attributes="$attributes"
>
    <x-moonshine::fields-group
        :components="$element->getFields()"
        :container="true"
    />
</x-moonshine::collapse>
