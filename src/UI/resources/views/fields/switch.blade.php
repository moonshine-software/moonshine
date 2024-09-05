@props([
    'name' => '',
    'formName' => '',
    'onValue' => '',
    'value' => '',
    'offValue' => '',
    'isChecked' => false,
])
<x-moonshine::form.switcher
    :attributes="$attributes"
    :name="$name"
    :onValue="$onValue"
    :offValue="$offValue"
    :value="($onValue == $value ? $onValue : $offValue)"
    :checked="$isChecked"
/>
