<div x-data="asyncData">
    <x-moonshine::form.switcher
        :attributes="$element->attributes()"
        :id="$element->id()"
        :name="$element->name()"
        :onValue="$element->getOnValue()"
        :offValue="$element->getOffValue()"
        :@change="(($autoUpdate ?? false) ? 'updateColumn(`'.route('moonshine.update-column').'`, `'.$element->field().'`, `'.$item->getKey().'`, `'.str_replace('\\', '\\\\', get_class($item)).'`, $event.target.checked)' : 'true')"
        :value="($element->getOnValue() == $element->formViewValue($item) ? '1' : '0')"
        :checked="$element->getOnValue() == $element->formViewValue($item)"
    />
</div>
