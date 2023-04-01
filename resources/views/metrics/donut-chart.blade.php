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
            series: @json($element->getValues()),
            tooltip: {
                y: {
                    formatter: function (val) {
                        return `${val}`
                    },
                    title: {
                        formatter: function (seriesName) {
                            return `"${seriesName}":`
                        },
                    },
                },
            },
            labels: @json($element->labels()),
            chart: {
                height: 350,
                type: "donut",
            },
            stroke: {
                colors: "transparent",
            },
            plotOptions: {
                pie: {
                    expandOnClick: false,
                },
            },
        }

        let chart_{{ $element->id() }} = new ApexCharts(chart_element_{{ $element->id() }}, options_{{ $element->id() }})
        setTimeout(() => {
            chart_{{ $element->id() }}.render()
        }, 300)
    </script>
@endpush
