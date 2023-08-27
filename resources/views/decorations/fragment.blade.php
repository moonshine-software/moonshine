@fragment($element->getName())
<x-moonshine::fields-group
    :components="$element->getFields()"
/>
@endfragment
