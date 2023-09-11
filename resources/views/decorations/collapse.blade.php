<x-moonshine::collapse :show="$element->isShow()"
                       :title="$element->label()"
                       :persist="true"
                       :attributes="$element->attributes()"
>
    <x-moonshine::fields-group
        :components="$element->getFields()"
        :container="true"
    />
</x-moonshine::collapse>
