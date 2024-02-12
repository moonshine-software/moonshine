<x-moonshine::collapse :open="$element->isOpen()"
                       :title="$element->label()"
                       :persist="$element->isPersist()"
                       :attributes="$attributes"
>
    <x-moonshine::fields-group
        :components="$element->getFields()"
        :container="true"
    />
</x-moonshine::collapse>
