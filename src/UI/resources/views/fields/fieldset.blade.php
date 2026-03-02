@props([
    'label' => '',
    'fields' => [],
])
<x-moonshine::form.fieldset
    :label="$label"
    :labelRaw="$labelRaw"
    :escapeLabel="$escapeLabel"
    :attributes="$attributes"
>
    <div class="space-elements">
        <x-moonshine::fields-group
            :components="$fields"
        />
    </div>
</x-moonshine::form.fieldset>
