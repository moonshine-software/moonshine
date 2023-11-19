@fragment($element->getName())
    <div {{ $element->attributes() }}>
        <x-moonshine::fields-group
            :components="$element->getFields()"
        />
    </div>
@endfragment
