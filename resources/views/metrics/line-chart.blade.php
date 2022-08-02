<x-moonshine::block title="{{ $metric->label() }}">
    <canvas id="{{ $metric->id() }}"></canvas>

    <script>
        const chart_{{ $metric->id() }} = new Chart('{{ $metric->id() }}', {
            type: 'line',
            data: {
                datasets: [
                        @foreach($metric->lines() as $index => $lines)
                        @foreach($lines as $label => $data)
                    {
                        label: '{{ $label }}',
                        data: @json(array_values($data)),
                        borderColor: '{{ $metric->color($index) }}',
                    },
                    @endforeach
                    @endforeach
                ],
                labels: @json($metric->labels())
            },
            options: {
                responsive: true,
            }
        });
    </script>
</x-moonshine::block>

