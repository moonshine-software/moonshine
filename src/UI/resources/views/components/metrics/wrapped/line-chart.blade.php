@props([
    'label' => '',
    'labels' => [],
    'lines' => [],
    'colors' => [],
    'columnSpanValue' => 12,
    'adaptiveColumnSpanValue' => 12,
])
<x-moonshine::layout.column
    :colSpan="$columnSpanValue"
    :adaptiveColSpan="$adaptiveColumnSpanValue"
>
    <x-moonshine::layout.box class="grow">
        <x-moonshine::metrics.line
            :attributes="$attributes"
            :lines="$lines"
            :colors="$colors"
            :labels="$labels"
            :title="$label"
        />
    </x-moonshine::layout.box>
</x-moonshine::layout.column>


