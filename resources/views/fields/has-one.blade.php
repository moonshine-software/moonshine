@if($element->isResourceMode())
    @if($element->formViewValue($item))
        <x-moonshine::async-modal id="edit_{{ $item->getTable() }}_modal_{{ $element->id() }}_{{ $item->getKey() }}" route="{{ $element->resource()->route('edit', $element->formViewValue($item)->getKey() , query: ['relatable_mode' => 1, 'related_column' => $item->{$element->relation()}()->getForeignKeyName(), 'related_key' => $item->getKey()]) }}" class="inline-flex  items-center bg-transparent hover:bg-purple text-purple border border-purple hover:text-white hover:border-transparent font-semibold  py-2 px-4 rounded">
            <x-moonshine::icon
                icon="heroicons.pencil-square"
                size="4"
            />
            <span>{{ trans('moonshine::ui.edit') }}</span>
        </x-moonshine::async-modal>
    @elseif($item->exists)
        <x-moonshine::async-modal id="create_{{ $element->resource()->getModel()->getTable() }}_modal_{{ $element->id() }}" route="{{ $element->resource()->route('create', query: ['relatable_mode' => 1, 'related_column' => $item->{$element->relation()}()->getForeignKeyName(), 'related_key' => $item->getKey()]) }}" class="inline-flex  items-center bg-transparent hover:bg-purple text-purple border border-purple hover:text-white hover:border-transparent font-semibold  py-2 px-4 rounded">
            <x-moonshine::icon
                icon="heroicons.squares-plus"
                size="4"
            />
            <span>{{ trans('moonshine::ui.create') }}</span>
        </x-moonshine::async-modal>
    @endif
@else
    @include('moonshine::fields.table-fields', [
        'element' => $element,
        'resource' => $resource,
        'item' => $item,
        'toOne' => true,
        'model' => $element->formViewValue($item) ?? $element->getRelated($item),
        'level' => $level ?? 0
    ])
@endif
