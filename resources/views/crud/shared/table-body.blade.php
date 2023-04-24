@foreach($resources as $resourceItem)
    <tr class="bgc-{{ $resourceItem->trClass($resourceItem->getItem(), $loop->index) }}" style="{{ $resourceItem->trStyles($resourceItem->getItem(), $loop->index) }}">
        @if($resource->hasMassAction())
            <td class="w-10 text-center bgc-{{ $resourceItem->tdClass($resourceItem->getItem(), $loop->index, 0) }}" style="{{ $resourceItem->tdStyles($resourceItem->getItem(), $loop->index, 0) }}">
                <x-moonshine::form.input type="checkbox"
                     @change="actions('row')"
                     name="items[{{ $resourceItem->getItem()->getKey() }}]"
                     class="tableActionRow"
                     value="{{ $resourceItem->getItem()->getKey() }}"
                />
            </td>
        @endif

        @foreach($resourceItem->getFields()->indexFields() as $index => $field)
            <td class="bgc-{{ $resourceItem->tdClass($resourceItem->getItem(), $loop->parent->index, $index + 1) }}"
                style="{{ $resourceItem->tdStyles($resourceItem->getItem(), $loop->parent->index, $index + 1) }}"
            >
                {!! $field->isSee($resourceItem->getItem()) ? $field->indexViewValue($resourceItem->getItem()): '' !!}
            </td>
        @endforeach

        @if(!$resource->isPreviewMode())
            <td class="bgc-{{ $resourceItem->tdClass($resourceItem->getItem(), $loop->index, count($resourceItem->getFields()->indexFields()) + 1) }}"
                style="{{ $resourceItem->tdStyles($resourceItem->getItem(), $loop->index, count($resourceItem->getFields()->indexFields()) + 1) }}"
            >
                @include("moonshine::crud.shared.table-row-actions", ["resource" => $resourceItem])
            </td>
        @endif
    </tr>
@endforeach
