<x-moonshine::column
    :colSpan="$item->columnSpanValue()"
    :adaptiveColSpan="$item->adaptiveColumnSpanValue()"
>
    <x-moonshine::box
        class="grow"
    >
        <div id="{{ $item->id() }}" class="chart"></div>
    </x-moonshine::box>
</x-moonshine::column>

@push('scripts')
    <script>
        let chart_element_{{ $item->id() }} = document.getElementById("{{ $item->id() }}")

        let options = {
        }

        let options_{{ $item->id() }} = {
            series: @json($item->getValues()),
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
            labels: @json($item->labels()),
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

        let chart_{{ $item->id() }} = new ApexCharts(chart_element_{{ $item->id() }}, options_{{ $item->id() }})
        setTimeout(() => {
            chart_{{ $item->id() }}.render()
        }, 300)
    </script>
@endpush
