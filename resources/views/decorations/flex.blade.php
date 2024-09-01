<div
    {{ $attributes
        ->class([
            'form-group',
            'sm:flex',
            'sm:flex-row',
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
