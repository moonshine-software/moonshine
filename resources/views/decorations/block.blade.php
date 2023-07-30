<x-moonshine::box
    :title="$element->label()"
>
    <x-moonshine::fields-group
        :components="$element->getFields()"
    />
</x-moonshine::box>
