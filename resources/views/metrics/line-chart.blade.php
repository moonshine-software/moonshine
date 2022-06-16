<x-moonshine::block title="{{ $item->label() }}">
    <canvas id="{{ $item->id() }}" ></canvas>

    <script>
      const chart_{{ $item->id() }} = new Chart('{{ $item->id() }}', {
        type: 'line',
        data: {
          datasets: [
                  @foreach($item->lines() as $index => $lines)
                  @foreach($lines as $label => $data)
            {
              label: '{{ $label }}',
              data: @json(array_values($data)),
              borderColor: '{{ $item->color($index) }}',
            },
              @endforeach
              @endforeach
          ],
          labels: @json($item->labels())
        },
        options: {
          responsive: true,
        }
      });
    </script>
</x-moonshine::block>

