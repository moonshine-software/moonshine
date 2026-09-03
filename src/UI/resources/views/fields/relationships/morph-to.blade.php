@props([
    'value' => '',
    'typeValue' => '',
    'types' => [],
    'values' => [],
    'column' => '',
    'morphType' => '',
    'morphTypeName' => '',
    'morphTypeAttributes' => null,
    'isNullable' => false,
    'isSearchable' => false,
    'isAsyncSearch' => false,
    'asyncSearchUrl' => '',
    'settings' => [],
    'plugins' => [],
])
<div x-data="{morphType: @js($typeValue)}"
     class="flex items-center gap-x-2"
>
    <div class="sm:w-1/4 w-full">
        <x-moonshine::form.select
            :attributes="$morphTypeAttributes"
            :name="$morphTypeName"
            x-model="morphType"
            required="required"
            :values="$types"
        />
    </div>

    <div class="sm:w-3/4 w-full">
        <x-moonshine::form.select
            :attributes="$attributes"
            :nullable="$isNullable"
            :searchable="true"
            data-async-extra-key="{{ $morphType }}"
            x-bind:data-async-extra="morphType"
            x-effect="morphClear(morphType)"
            :value="$value"
            :values="$values"
            :asyncRoute="$isAsyncSearch ? $asyncSearchUrl : null"
            data-async-on-init="true"
            data-async-on-init-dropdown="true"
            :settings="$settings"
            :plugins="$plugins"
        >
        </x-moonshine::form.select>
    </div>

</div>
