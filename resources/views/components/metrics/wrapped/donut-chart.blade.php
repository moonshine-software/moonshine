@props([
    'label' => '',
    'labels' => [],
    'values' => [],
    'colors' => [],
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
            :colors="$colors"
            :title="$label"
        />
    </x-moonshine::layout.box>
</x-moonshine::layout.column>
