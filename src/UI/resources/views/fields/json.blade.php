@props([
    'rows' => [],
    'fields' => [],
    'controls' => [],
    'inputName' => null,
    'removable' => true,
    'creatable' => true,
    'creatableLimit' => null,
    'hideCreateButton' => false,
    'showCreateButtonText' => true,
    'showCreateButtonIcon' => true,
    'createButton' => null,
    'buttons' => null,
    'removeButton' => null,
    'removeButtonAttributes' => [],
    'reorderable' => false,
    'orientation' => 'horizontal',
    'keyValue' => false,
    'onlyValue' => false,
    'objectMode' => false,
    'filterEmpty' => true,
    'emptyMessage' => '',
])

<x-moonshine::json
    :rows="$rows"
    :fields="$fields"
    :controls="$controls"
    :input-name="$inputName"
    :removable="$removable"
    :creatable="$creatable"
    :creatable-limit="$creatableLimit"
    :hide-create-button="$hideCreateButton"
    :show-create-button-text="$showCreateButtonText"
    :show-create-button-icon="$showCreateButtonIcon"
    :create-button="$createButton"
    :buttons="$buttons"
    :remove-button="$removeButton"
    :remove-button-attributes="$removeButtonAttributes"
    :reorderable="$reorderable"
    :orientation="$orientation"
    :key-value="$keyValue"
    :only-value="$onlyValue"
    :object-mode="$objectMode"
    :filter-empty="$filterEmpty"
    :empty-message="$emptyMessage"
    {{ $attributes }}
/>
