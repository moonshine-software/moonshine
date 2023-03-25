<div x-data="asyncData">
    <x-moonshine::form.switcher
        :attributes="$field->attributes()"
        :id="$field->id()"
        :name="$field->name()"
        :onValue="$field->getOnValue()"
        :offValue="$field->getOffValue()"
        :@change="(($autoUpdate ?? false) ? 'updateColumn(`'.route('moonshine.update-column').'`, `'.$field->field().'`, `'.$item->getKey().'`, `'.str_replace('\\', '\\\\', get_class($item)).'`, $event.target.checked)' : 'true')"
        :value="($field->getOnValue() == $field->formViewValue($item) ? '1' : '0')"
        :checked="$field->getOnValue() == $field->formViewValue($item)"
    />
</div>
