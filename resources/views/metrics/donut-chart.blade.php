<x-moonshine::column
    :colSpan="$element->columnSpanValue()"
    :adaptiveColSpan="$element->adaptiveColumnSpanValue()"
>
    <x-moonshine::box class="grow">
        <div
            id="{{ $element->id() }}"
            class="chart"
            x-data="charts({
                series: {{ json_encode($element->getValues()) }},
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return `${val}`
                        },
                        title: {
                            formatter: function (seriesName) {
                                return `${seriesName}:`
                            },
                        },
                    },
                },
                labels: {{ json_encode($element->labels()) }},
                chart: {
                    height: 350,
                    type: 'donut',
                },
                stroke: {
                    colors: 'transparent',
                },
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    label: '{{ $element->label() }}',
                                    showAlways: false,
                                    show: true
                                }
                            }
                        }
                    },
                },
            })"
        ></div>
    </x-moonshine::box>
</x-moonshine::column>
