@props([
    'label' => '',
    'labels' => [],
    'values' => [],
    'colors' => [],
    'decimals' => 3,
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
            :decimals="$decimals"
            :title="$label"
        />
    </x-moonshine::layout.box>
</x-moonshine::layout.column>
