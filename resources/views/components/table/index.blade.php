@props([
    'values' => false,
    'columns' => false,
    'thead',
    'tbody',
    'tfoot'
])
<!-- Table -->
<div class="table-responsive">
    <table {{ $attributes->merge(['class' => 'table-list']) }}>
        <thead {{ $thead?->attributes }}>
            <tr>
                @if(is_array($columns))
                    @foreach($columns as $name => $label)
                        <th>
                            {{ $label }}
                        </th>
                    @endforeach
                @endif

                {{ $thead ?? '' }}
            </tr>
        </thead>
        <tbody  {{ $tbody?->attributes }}>
            @if(is_array($values))
                @foreach($values as $index => $data)
                    <tr>
                        @foreach($columns as $name => $label)
                            <td>
                                {!! $data[$name] ?? '' !!}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endif

            {{ $tbody ?? '' }}
        </tbody>
        @if($tfoot ?? false)
        <tfoot  {{ $tfoot->attributes }}>
            <tr>
                {{ $tfoot }}
            </tr>
        </tfoot>
        @endif
    </table>
</div>
