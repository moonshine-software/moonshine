@props([
    'simple' => false,
    'values' => false,
    'columns' => false,
    'notfound' => false,
    'responsive' => true,
    'thead',
    'tbody',
    'tfoot',
])
@if(isset($tbody) || (is_iterable($values) && count($values)))
    <!-- Table -->
    <div @if ($responsive) class="table-responsive" @endif>
        <table {{ $attributes
                ->merge(['class' => 'table' . (!$simple ? '-list' : '')])
        }}>
            <thead {{ isset($thead) ? $thead?->attributes : '' }}>
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
            <tbody  {{ $tbody?->attributes ?? '' }}>
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
                <tfoot {{ $tfoot->attributes }}>
                    <tr>
                        {{ $tfoot }}
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@elseif($notfound)
    <x-moonshine::alert type="default" class="my-4" icon="heroicons.no-symbol">
        {{ trans('moonshine::ui.notfound') }}
    </x-moonshine::alert>
@endif
