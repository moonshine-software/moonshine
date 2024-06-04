<x-moonshine::column
    :colSpan="$element->columnSpanValue()"
    :adaptiveColSpan="$element->adaptiveColumnSpanValue()"
>
    <x-moonshine::box class="grow">
        <x-moonshine::metrics.donut
            :attributes="$attributes->merge(['id' => $element->id()])"
            :values="$element->getValues()"
            :colors="$element->getColors()"
            :decimal="$element->getDecimal()"
            :labels="$element->labels()"
            :title="$element->label()"
        />
    </x-moonshine::box>
</x-moonshine::column>
