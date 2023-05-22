<x-moonshine::collapse :show="$element->isShow()"
                       :title="$element->label()"
                       :persist=true
>
    <x-moonshine::resource-renderable
        :components="$element->getFields()"
        :item="$item"
        :resource="$resource"
        :container="true"
    />
</x-moonshine::collapse>
