<div x-data="{
         fromDate: '{{ $element->formViewValue($item)['from'] ?? '' }}',
         toDate: '{{ $element->formViewValue($item)['to'] ?? '' }}'
     }"
     class="form-group form-group-inline"
>
    <x-moonshine::form.input
        :attributes="$element->attributes()->merge([
            'name' => $element->name('from')
        ])"
        type="date"
        x-bind:max="toDate"
        x-model="fromDate"
    />

    <x-moonshine::form.input
        :attributes="$element->attributes()->merge([
            'name' => $element->name('to')
        ])"
        type="date"
        x-bind:min="fromDate"
        x-model="toDate"
    />
</div>
