@props([
    'vertical' => false,
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
                @if($vertical) <tr {{ $row->trAttributes($index) }}>
                    <td {{ $row->tdAttributes($index, 0) }}>
                        {{$field->label()}}
                    </td>
                @endif

                <td {{ $vertical
                        ? $row->tdAttributes($index, 1)
                        : $row->tdAttributes($loop->parent->index, $index + $actions->isNotEmpty()) }}
                >
                    {!! $field->isSee($field->value())
                        ? $field->preview()
                        : ''
                    !!}
                </td>

                @if($vertical)
                    </tr>
                @endif
        @endforeach

        @if(!$vertical)
            <td {{ $row->tdAttributes($loop->index, $row->getFields()->count() + $actions->isNotEmpty()) }}>
                <x-moonshine::table.actions
                    :actions="$row->getActions()"
                />
            </td>
        @endif
    @if(!$vertical) </tr> @endif
@endforeach
