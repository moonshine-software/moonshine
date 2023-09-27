<x-moonshine::column
    :colSpan="$element->columnSpanValue()"
    :adaptiveColSpan="$element->adaptiveColumnSpanValue()" xmlns:x-moonshine="http://www.w3.org/1999/html"
>
    <x-moonshine::box
        class="box-shadow zoom-in h-full p-0"
    >
        <x-moonshine::metrics.value
            :attributes="$element->attributes()"
            :title="$element->label()"
            :icon="$element->getIcon(6, 'secondary')"
            :progress="$element->isProgress()"
            :value="$element->valueResult()"
            :simpleValue="$element->simpleValue()"
        />
    </x-moonshine::box>
</x-moonshine::column>
