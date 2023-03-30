<x-moonshine::column
    :colSpan="$element->columnSpanValue()"
    :adaptiveColSpan="$element->adaptiveColumnSpanValue()"
>
    <x-moonshine::box
        class="grow"
    >
        <div id="{{ $element->id() }}" class="chart"></div>
    </x-moonshine::box>
</x-moonshine::column>

@push('scripts')
    <script>
        let chart_element_{{ $element->id() }} = document.getElementById("{{ $element->id() }}")
        let options_{{ $element->id() }} = {
            series: [
                    @foreach($element->lines() as $index => $lines)
                    @foreach($lines as $label => $data)
                {
                    name: "{{ $label }}",
                    data: @json(array_values($data)),
                },
                @endforeach
                @endforeach
            ],
            colors: @json($element->colors()),
            labels: @json($element->labels()),
            chart: {
                height: 300,
                type: "line",
            },
            yaxis: {
                title: {
                    text: "{{ $element->label() }}",
                    style: {
                        fontWeight: 400,
                    },
                },
            },
        }

        let chart_{{ $element->id() }} = new ApexCharts(chart_element_{{ $element->id() }}, options_{{ $element->id() }})
        setTimeout(() => {
            chart_{{ $element->id() }}.render()
        }, 300)
    </script>
@endpush


