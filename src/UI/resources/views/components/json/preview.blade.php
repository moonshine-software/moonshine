@props([
    'label' => '',
    'items' => [],
    'objectMode' => false,
    'tableMode' => false,
    'tableAttributes' => null,
    'tableBuilder' => null,
    'tableSimple' => false,
    'tableSticky' => false,
    'nested' => false,
])

<x-moonshine::layout.div class="space-elements">
    @if($label !== '')
        <x-moonshine::form.label>
            {{ $label }}
        </x-moonshine::form.label>
    @endif

    @if($items === [])
        @include('moonshine::fields.preview', ['value' => '-'])
    @elseif($tableMode)
        @php
            $columns = [];
            $values = [];

            foreach ($items as $rowIndex => $item) {
                foreach (($item['fields'] ?? []) as $fieldIndex => $field) {
                    $column = (string) $fieldIndex;

                    $columns[$column] = e($field['label'] ?? '');
                    $values[$rowIndex][$column] = view('moonshine::components.json.preview-cell', [
                        'field' => $field,
                    ])->render();
                }
            }
        @endphp

        @include('moonshine::components.table.index', [
            'columns' => $columns,
            'values' => false,
            'tbody' => new \Illuminate\Support\HtmlString(view('moonshine::components.json.preview-table-body', [
                'columns' => $columns,
                'values' => $values,
                'tableBuilder' => $tableBuilder,
            ])->render()),
            'attributes' => $tableAttributes ?? new \Illuminate\View\ComponentAttributeBag(),
            'simple' => $tableSimple,
            'sticky' => $tableSticky,
        ])
    @else
        @foreach($items as $item)
            <x-moonshine::layout.div class="space-elements">
                @foreach($item['fields'] ?? [] as $field)
                    <x-moonshine::layout.grid :gap="2">
                        <x-moonshine::layout.column :adaptive-col-span="12" :col-span="2">
                            <div class="form-label">{{ $field['label'] ?? '' }}</div>
                        </x-moonshine::layout.column>

                        <x-moonshine::layout.column :adaptive-col-span="12" :col-span="10">
                            <div class="form-group form-group-inline">
                                @include('moonshine::components.json.preview-cell', ['field' => $field])
                            </div>
                        </x-moonshine::layout.column>
                    </x-moonshine::layout.grid>
                @endforeach
            </x-moonshine::layout.div>

            @if($nested && ! $loop->last)
                <x-moonshine::layout.divider />
            @endif
        @endforeach
    @endif
</x-moonshine::layout.div>
