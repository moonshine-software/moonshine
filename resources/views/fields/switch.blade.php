<div x-data="asyncData">
    <x-moonshine::form.switcher
        :attributes="$element->attributes()->except('x-on:change')"
        :id="$element->id()"
        :name="$element->name()"
        :onValue="$element->getOnValue()"
        :offValue="$element->getOffValue()"
        :@change="(($autoUpdate ?? false) && $element->resource()
            ? 'updateColumn(
                `'.$element->resource()?->route('update-column', $item->getKey()).'`,
                `'.$element->field().'`,
                $event.target.checked ? `'.$element->getOnValue().'` : `'.$element->getOffValue().'`
            )'
            : $element->attributes()->get('x-on:change')
        )"
        :value="($element->getOnValue() == $element->formViewValue($item) ? $element->getOnValue() : $element->getOffValue())"
        :checked="$element->getOnValue() == $element->formViewValue($item)"
    />
</div>
