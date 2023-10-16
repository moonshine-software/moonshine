<x-moonshine::column
    :colSpan="$element->columnSpanValue()"
    :adaptiveColSpan="$element->adaptiveColumnSpanValue()"
>
    <x-moonshine::box class="grow">
        <x-moonshine::metrics.line
            :attributes="$attributes->merge(['id' => $element->id()])"
            :lines="$element->lines()"
            :colors="$element->colors()"
            :labels="$element->labels()"
            :title="$element->label()"
        />
    </x-moonshine::box>
</x-moonshine::column>


