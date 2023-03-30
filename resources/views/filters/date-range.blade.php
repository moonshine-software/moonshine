<div x-data="date_range_{{ $element->id() }}()" class="form-group form-group-inline">
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

<script>
    function date_range_{{ $element->id()}}() {
        return {
            fromDate: '{{ $element->formViewValue($item)['from'] ?? '' }}',
            toDate: '{{ $element->formViewValue($item)['to'] ?? '' }}',
        }
    }
</script>
