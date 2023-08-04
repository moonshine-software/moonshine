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

        @foreach($row->getFields() as $index => $field)
            @if($vertical) <tr {{ $row->trAttributes($loop->index) }}> <td>{{$field->label()}}</td> @endif
                <td {{ $row->tdAttributes($loop->parent->index, $index + 1) }}>
                    {!! $field->isSee($field->value())
                        ? ($editable ? $field->render() : $field->preview())
                        : ''
                    !!}
                </td>
            @if($vertical) </tr> @endif
        @endforeach

        @if(!$vertical)
            <td {{ $row->tdAttributes($loop->index, $row->getFields()->count() + 1) }}>
                <x-moonshine::table.actions
                    :actions="$row->getActions()"
                />
            </td>
        @endif
    @if(!$vertical) </tr> @endif
@endforeach
