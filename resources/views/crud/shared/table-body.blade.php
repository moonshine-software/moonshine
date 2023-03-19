@foreach($items as $item)
    <tr style="{{ $resource->trStyles($item, $loop->index) }}">
        @if($resource->isMassAction())
            <td class="w-10 text-center" style="{{ $resource->tdStyles($item, $loop->index, 0) }}">
                <x-moonshine::form.input type="checkbox"
                     @change="actions($event.target)"
                     name="items[{{ $item->getKey() }}]"
                     class="tableActionRow"
                     value="{{ $item->getKey() }}"
                />
            </td>
        @endif

        @foreach($resource->indexFields() as $index => $field)
            <td style="{{ $resource->tdStyles($item, $loop->parent->index, $index + 1) }}"
            >
                {!! $field->indexViewValue($item) !!}
            </td>
        @endforeach

        @if(!$resource->isPreviewMode())
            <td style="{{ $resource->tdStyles($item, $loop->index, count($resource->indexFields()) + 1) }}"
            >
                @include("moonshine::crud.shared.table-row-actions", ["item" => $item, "resource" => $resource])
            </td>
        @endif
    </tr>
@endforeach
