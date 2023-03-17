@if($field->isResourceMode())
    @if($field->formViewValue($item))
        <x-moonshine::async-modal id="edit_{{ $item->getTable() }}_modal_{{ $field->id() }}_{{ $item->getKey() }}" route="{{ $field->resource()->route('edit', $field->formViewValue($item)->getKey() , query: ['relatable_mode' => 1, 'related_column' => $item->{$field->relation()}()->getForeignKeyName(), 'related_key' => $item->getKey()]) }}" class="inline-flex  items-center bg-transparent hover:bg-purple text-purple border border-purple hover:text-white hover:border-transparent font-semibold  py-2 px-4 rounded">
            <x-moonshine::icon
                icon="heroicons.pencil-square"
                size="4"
            />
            <span>{{ trans('moonshine::ui.edit') }}</span>
        </x-moonshine::async-modal>
    @elseif($item->exists)
        <x-moonshine::async-modal id="create_{{ $field->resource()->getModel()->getTable() }}_modal_{{ $field->id() }}" route="{{ $field->resource()->route('create', query: ['relatable_mode' => 1, 'related_column' => $item->{$field->relation()}()->getForeignKeyName(), 'related_key' => $item->getKey()]) }}" class="inline-flex  items-center bg-transparent hover:bg-purple text-purple border border-purple hover:text-white hover:border-transparent font-semibold  py-2 px-4 rounded">
            <x-moonshine::icon
                icon="heroicons.squares-plus"
                size="4"
            />
            <span>{{ trans('moonshine::ui.create') }}</span>
        </x-moonshine::async-modal>
    @endif
@else
    @include('moonshine::fields.table-fields', [
        'field' => $field,
        'resource' => $resource,
        'item' => $item,
        'model' => $field->formViewValue($item) ?? $field->getRelated($item),
        'level' => $level ?? 0
    ])
@endif
