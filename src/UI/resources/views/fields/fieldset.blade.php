@props([
    'escapeLabel' => null,
    'label' => '',
    'labelHtml' => null,
    'fields' => [],
])
<x-moonshine::form.fieldset :label="$label" :label-html="$labelHtml" :escape-label="$escapeLabel" :attributes="$attributes">
    <div class="space-elements">
        <x-moonshine::fields-group
            :components="$fields"
        />
    </div>
</x-moonshine::form.fieldset>
