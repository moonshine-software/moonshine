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
])
@if(isset($tbody) || (is_iterable($values) && count($values)))
    <!-- Table -->
    <div @class(['table-responsive' => $responsive, 'table-sticky' => $sticky])>
        <table {{ $attributes->merge(['class' => 'table' . (!$simple ? '-list' : '')]) }}
               x-id="['table-component']" :id="$id('table-component')"
        >
            <thead {{ $headAttributes ??  $thead->attributes ?? '' }}>
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
@elseif($notfound)
    <x-moonshine::alert type="default" class="my-4" icon="s.no-symbol">
        {{ trans('moonshine::ui.notfound') }}
    </x-moonshine::alert>
@endif
