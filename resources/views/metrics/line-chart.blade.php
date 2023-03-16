<x-moonshine::box
    :adaptiveColSpan="$item->adaptiveColumnSpanValue()"
    :colSpan="$item->columnSpanValue()"
    class="grow"
>
    <div id="{{ $item->id() }}" class="chart"></div>
</x-moonshine::box>

@push('scripts')
    <script>
        let chart_element_{{ $item->id() }} = document.getElementById("{{ $item->id() }}")
        let options_{{ $item->id() }} = {
            series: [
                    @foreach($item->lines() as $index => $lines)
                    @foreach($lines as $label => $data)
                {
                    name: "{{ $label }}",
                    data: @json(array_values($data)),
                },
                @endforeach
                @endforeach
            ],
            colors: @json($item->colors()),
            labels: @json($item->labels()),
            chart: {
                height: 300,
                type: "line",
            },
            yaxis: {
                title: {
                    text: "{{ $item->label() }}",
                    style: {
                        fontWeight: 400,
                    },
                },
            },
        }

        let chart_{{ $item->id() }} = new ApexCharts(chart_element_{{ $item->id() }}, options_{{ $item->id() }})
        setTimeout(() => {
            chart_{{ $item->id() }}.render()
        }, 300)
    </script>
@endpush


