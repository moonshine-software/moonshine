
@foreach($resources as $resourceItem)
    <tr class="bgc-{{ $resourceItem->trClass($loop->index) }}" style="{{ $resourceItem->trStyles($loop->index) }}">
        @if($resource->hasMassAction())
            <td class="w-10 text-center bgc-{{ $resourceItem->tdClass($loop->index, 0) }}" style="{{ $resourceItem->tdStyles($loop->index, 0) }}">
                <x-moonshine::form.input type="checkbox"
                     @change="actions('row')"
                     name="items[{{ $resourceItem->getItem()->getKey() }}]"
                     class="tableActionRow"
                     value="{{ $resourceItem->getItem()->getKey() }}"
                />
            </td>
        @endif

        @foreach($resource->getFields()->indexFields() as $index => $field)
            <td class="bgc-{{ $resourceItem->tdClass($loop->parent->index, $index + 1) }}"
                style="{{ $resourceItem->tdStyles($loop->parent->index, $index + 1) }}"
            >
                {!! $field->isSee($resourceItem->getItem()) ? $field->indexViewValue($resourceItem->getItem()): '' !!}
            </td>
        @endforeach

        @if(!$resource->isPreviewMode())
            <td class="bgc-{{ $resourceItem->tdClass($loop->index, count($resource->getFields()->indexFields()) + 1) }}"
                style="{{ $resource->tdStyles($loop->index, count($resource->getFields()->indexFields()) + 1) }}"
            >
                @include("moonshine::crud.shared.table-row-actions", ["item" => $resourceItem->getItem(), "resource" => $resource])
            </td>
        @endif
    </tr>
@endforeach
