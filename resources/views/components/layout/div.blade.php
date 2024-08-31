<div {{ $attributes }} >
    <x-moonshine::fields-group
        :components="$element->getFields()"
    />

    {{ $slot ?? '' }}
</div>
