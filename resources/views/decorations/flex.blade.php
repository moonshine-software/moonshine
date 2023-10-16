<div
    {{ $attributes
        ->class([
            'sm:flex',
            'gap-4' => !$element->isWithoutSpace(),
            'items-' . $element->getItemsAlign(),
            'justify-' . $element->getJustifyAlign()
        ])
    }}
>
    <x-moonshine::fields-group
        :components="$element->getFields()"
        :container="true"
    />
</div>
