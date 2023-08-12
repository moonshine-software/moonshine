<div x-data="asyncData">
    <x-moonshine::form.switcher
        :attributes="$element->attributes()->except('x-on:change')"
        :id="$element->id()"
        :name="$element->name()"
        :onValue="$element->getOnValue()"
        :offValue="$element->getOffValue()"
        :@change="(($autoUpdate ?? false)
            ? 'updateColumn(
                `'.$element->getUpdateUrl().'`,
                `'.$element->column().'`,
                $event.target.checked ? `'.$element->getOnValue().'` : `'.$element->getOffValue().'`
            )'
            : $element->attributes()->get('x-on:change')
        )"
        :value="($element->getOnValue() == $element->value() ? $element->getOnValue() : $element->getOffValue())"
        :checked="$element->isChecked()"
    />
</div>
