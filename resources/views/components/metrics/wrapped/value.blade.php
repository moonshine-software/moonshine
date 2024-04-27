@props([
    'label' => '',
    'icon' => '',
    'columnSpanValue' => 12,
    'adaptiveColumnSpanValue' => 12,
    'isProgress' => false,
    'valueResult' => '',
    'simpleValue' => '',
])
<x-moonshine::layout.column
    :colSpan="$columnSpanValue"
    :adaptiveColSpan="$adaptiveColumnSpanValue"
    xmlns:x-moonshine="http://www.w3.org/1999/html"
>
    <x-moonshine::layout.box
        class="box-shadow zoom-in h-full p-0"
    >
        <x-moonshine::metrics.value
            :attributes="$attributes"
            :title="$label"
            :icon="$icon"
            :progress="$isProgress"
            :value="$valueResult"
            :simpleValue="$simpleValue"
        />
    </x-moonshine::layout.box>
</x-moonshine::layout.column>
