<x-moonshine::column
    :colSpan="$element->columnSpanValue()"
    :adaptiveColSpan="$element->adaptiveColumnSpanValue()"
>
    <x-moonshine::box class="grow">
        <div
            id="{{ $element->id() }}"
            class="chart"
            x-data="charts({
                series: [
                @foreach($element->lines() as $index => $lines)
                    @foreach($lines as $label => $data)
                    {
                        name: '{{ $label }}',
                        data: {{ json_encode(array_values($data)) }},
                    },
                    @endforeach
                @endforeach
                ],
                colors: {{ json_encode($element->colors()) }},
                labels: {{ json_encode($element->labels()) }},
                chart: {
                    height: 300,
                    type: 'line',
                },
                yaxis: {
                    title: {
                        text: '{{ $element->label() }}',
                        style: {
                            fontWeight: 400,
                        },
                    },
                },
            })"
        ></div>
    </x-moonshine::box>
</x-moonshine::column>


