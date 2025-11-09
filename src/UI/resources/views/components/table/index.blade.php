@props([
    'simple' => false,
    'values' => false,
    'columns' => false,
    'notfound' => false,
    'responsive' => true,
    'sticky' => false,
    'thead',
    'tbody',
    'tfoot',
    'headAttributes' => '',
    'bodyAttributes' => '',
    'footAttributes' => '',
    'translates' => [],
])
@if(isset($tbody) || (is_iterable($values) && count($values)))

    <!-- Table -->
    @if(!$simple)<div class="table-container">@endif
        <div @class(['table-responsive' => $responsive, 'table-sticky' => $sticky])>
            <table {{ $attributes->merge(['class' => 'table' . (!$simple ? ' table-list' : '')]) }}
                x-id="['table-component']" :id="$id('table-component')"
            >
                <thead {{ $headAttributes ??  $thead->attributes ?? '' }}>
                @if(is_iterable($columns))
                    <tr>
                        @foreach($columns as $index => $label)
                            <th>
                                {!! $label !!}
                            </th>
                        @endforeach
                    </tr>
                @endif
                {{ $thead ?? '' }}
                </thead>
                <tbody  {{ $bodyAttributes ?? $tbody->attributes ?? '' }}>
                @if(is_iterable($values))
                    @foreach($values as $index => $data)
                        <tr>
                            @foreach($columns as $name => $label)
                                <td>
                                    {!! isset($data[$name]) && is_scalar($data[$name]) ? $data[$name] : '' !!}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endif

                {{ $tbody ?? '' }}
                </tbody>

                @if($tfoot ?? false)
                    <tfoot {{ $footAttributes ?? $tfoot->attributes ?? '' }}>
                    {{ $tfoot }}
                    </tfoot>
                @endif
            </table>
        </div>
    @if(!$simple)</div>@endif
@elseif($notfound)
    <x-moonshine::alert type="default" class="my-4" icon="s.no-symbol">
        {{ $translates['notfound'] ?? 'Records not found' }}
    </x-moonshine::alert>
@endif
