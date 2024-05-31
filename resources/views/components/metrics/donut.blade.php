@props([
    'title' => '',
    'values' => [],
    'labels' => [],
])
<div
    {{ $attributes->merge(['class' => 'chart']) }}
    x-data="charts({
                series: {{ json_encode($values) }},
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
                labels: {{ json_encode($labels) }},
                chart: {
                    height: 350,
                    type: 'donut',
                },
                stroke: {
                    colors: ['transparent'],
                },
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    label: '{{ $title }}',
                                    showAlways: false,
                                    show: true
                                }
                            }
                        }
                    },
                },
            })"
></div>
