<div x-data="date_range_{{ $field->id() }}()" class="form-group form-group-inline">
    <x-moonshine::form.input
        :attributes="$field->attributes()->merge([
            'name' => $field->name('from')
        ])"
        type="date"
        x-bind:max="toDate"
        x-model="fromDate"
    />

    <x-moonshine::form.input
        :attributes="$field->attributes()->merge([
            'name' => $field->name('to')
        ])"
        type="date"
        x-bind:min="fromDate"
        x-model="toDate"
    />
</div>

<script>
    function date_range_{{ $field->id()}}() {
        return {
            fromDate: '{{ $field->formViewValue($item)['from'] ?? '' }}',
            toDate: '{{ $field->formViewValue($item)['to'] ?? '' }}',
        }
    }
</script>
