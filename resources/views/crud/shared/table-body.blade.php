@foreach($items as $item)
    <tr class="bgc-{{ $resource->trClass($item, $loop->index) }}" style="{{ $resource->trStyles($item, $loop->index) }}">
        @if($resource->hasMassAction())
            <td class="w-10 text-center bgc-{{ $resource->tdClass($item, $loop->index, 0) }}" style="{{ $resource->tdStyles($item, $loop->index, 0) }}">
                <x-moonshine::form.input type="checkbox"
                     @change="actions('row')"
                     name="items[{{ $item->getKey() }}]"
                     class="tableActionRow"
                     value="{{ $item->getKey() }}"
                />
            </td>
        @endif

        @foreach($resource->getFields()->indexFields() as $index => $field)
            <td class="bgc-{{ $resource->tdClass($item, $loop->parent->index, $index + 1) }}"
                style="{{ $resource->tdStyles($item, $loop->parent->index, $index + 1) }}"
            >
                {!! $field->isSee($item) ? $field->indexViewValue($item): '' !!}
            </td>
        @endforeach

        @if(!$resource->isPreviewMode())
            <td class="bgc-{{ $resource->tdClass($item, $loop->index, count($resource->getFields()->indexFields()) + 1) }}"
                style="{{ $resource->tdStyles($item, $loop->index, count($resource->getFields()->indexFields()) + 1) }}"
            >
                @include("moonshine::crud.shared.table-row-actions", ["item" => $item, "resource" => $resource])
            </td>
        @endif
    </tr>
@endforeach
