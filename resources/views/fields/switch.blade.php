@php
    $uniqueId = uniqid();
@endphp

<x-moonshine::form.switcher
    :attributes="$field->attributes()"
    :id="$field->id($uniqueId)"
    :name="$field->name()"
    :onValue="$field->getOnValue()"
    :offValue="$field->getOffValue()"
    @change="change_switcher_{{ $field->id($uniqueId) }}($event.target.checked)"
    :value="($field->getOnValue() == $field->formViewValue($item) ? '1' : '0')"
    :checked="$field->getOnValue() == $field->formViewValue($item)"
/>

<script>
    async function change_switcher_{{ $field->id($uniqueId) }}(checked)
    {
        @if($autoUpdate ?? false)
            await fetch('{{ route('moonshine.auto-update') }}?' + new URLSearchParams({
            value: checked,
            key: '{{ $item->getKey() }}',
            model: '{{ str_replace('\\', '\\\\', get_class($item)) }}',
            field: '{{ $field->field() }}'
        }))
        @endif
    }
</script>
