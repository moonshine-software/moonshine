@props([
    'columns' => [],
    'values' => [],
    'tableBuilder' => null,
])

@foreach($values as $rowIndex => $data)
    @php
        $rowAttributes = new \Illuminate\View\ComponentAttributeBag(
            $tableBuilder?->getTrAttributes(null, $rowIndex) ?? []
        );
    @endphp

    <x-moonshine::table.row :attributes="$rowAttributes">
        @foreach($columns as $name => $label)
            @php
                $cellAttributes = new \Illuminate\View\ComponentAttributeBag(
                    $tableBuilder?->getTdAttributes(null, $rowIndex, $loop->index) ?? []
                );
            @endphp

            <x-moonshine::table.td :attributes="$cellAttributes">
                {!! isset($data[$name]) && is_scalar($data[$name]) ? $data[$name] : '' !!}
            </x-moonshine::table.td>
        @endforeach
    </x-moonshine::table.row>
@endforeach
