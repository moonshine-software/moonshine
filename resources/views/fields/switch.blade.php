<x-moonshine::form.switcher
    :attributes="$element->attributes()"
    :id="$element->id()"
    :name="$element->name()"
    :onValue="$element->getOnValue()"
    :offValue="$element->getOffValue()"
    :value="($element->getOnValue() == $value ? $element->getOnValue() : $element->getOffValue())"
    :checked="$element->isChecked()"
/>
