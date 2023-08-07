@props([
    'vertical' => false,
    'editable' => false,
    'rows',
    'actions',
])
@foreach($rows as $row)
    @if(!$vertical) <tr {{ $row->trAttributes($loop->index) }}> @endif
        @if(!$vertical && $actions->isNotEmpty())
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
        @endif

        @if(!$vertical)
            @foreach($row->getFields() as $cell => $field)
                <td {{ $row->tdAttributes($loop->parent->index, $cell + 1) }}>
                    {!! $field->isSee($field->value())
                        ? ($editable ? $field->render() : $field->preview())
                        : ''
                    !!}
                </td>
            @endforeach
        @else
            @php $i = 0; @endphp
            @foreach($row->getFields() as $cell => $field)
                <tr {{ $row->trAttributes($loop->index) }}>
                    <td {{ $row->tdAttributes($loop->parent->index, $cell + $i) }}>{{$field->label()}}</td>
                    @php $i++; @endphp
                    <td {{ $row->tdAttributes($loop->parent->index, $cell + $i) }}>
                        {!! $field->isSee($field->value())
                            ? ($editable ? $field->render() : $field->preview())
                            : ''
                        !!}
                    </td>
                </tr>
            @endforeach
        @endif

        @if(!$vertical)
            <td {{ $row->tdAttributes($loop->index, $row->getFields()->count() + 1) }}>
                <x-moonshine::table.actions
                    :actions="$row->getActions()"
                />
            </td>
        @endif
    @if(!$vertical) </tr> @endif
@endforeach
