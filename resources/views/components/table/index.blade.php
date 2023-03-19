@props([
    'values' => false,
    'columns' => false,
    'notfound' => false,
    'thead',
    'tbody',
    'tfoot',
])
@if(isset($thead, $tbody))
<!-- Table -->
<div class="table-responsive">
    <table {{ $attributes->merge(['class' => 'table' . ($crudMode ? '-list' : '')]) }}>
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
@elseif($notfound)
    <x-moonshine::alert>
        {{ trans('moonshine::ui.notfound') }}
    </x-moonshine::alert>
@endif
