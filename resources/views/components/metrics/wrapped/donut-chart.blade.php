@props([
    'label' => '',
    'labels' => [],
    'values' => [],
    'columnSpanValue' => 12,
    'adaptiveColumnSpanValue' => 12,
])
<x-moonshine::layout.column
    :colSpan="$columnSpanValue"
    :adaptiveColSpan="$adaptiveColumnSpanValue"
>
    <x-moonshine::layout.box class="grow">
        <x-moonshine::metrics.donut
            :attributes="$attributes"
            :values="$values"
            :labels="$labels"
            :title="$label"
        />
    </x-moonshine::layout.box>
</x-moonshine::layout.column>
