@foreach($rows as $row)
    <tr {{ $row->trAttributes($loop->index) }}>
        <td {{ $row->tdAttributes($loop->index, 0)
            ->merge(['class' => 'w-10 text-center']) }}
        >
            <x-moonshine::form.input type="checkbox"
                 @change="actions('row')"
                 name="items[{{ $row->getKey() }}]"
                 class="tableActionRow"
                 value="{{ $row->getKey() }}"
            />
        </td>

        @foreach($row->getFields() as $index => $field)
            <td {{ $row->tdAttributes($loop->parent->index, $index + 1) }}>
                {!! $field->isSee($field->value()) ? $field->preview(): '' !!}
            </td>
        @endforeach

        <td {{ $row->tdAttributes($loop->index, $row->getFields()->count() + 1) }}>
            @include('moonshine::crud.shared.table-row-actions', [
                'actions' => $row->getActions()
            ])
        </td>
    </tr>
@endforeach
