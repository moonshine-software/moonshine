@if($field->isResourceMode())
    @if($field->formViewValue($item))
        <x-moonshine::async-modal id="edit_modal_{{ $field->id() }}_{{ $item->getKey() }}" route="{{ $field->resource()->route('edit', $field->formViewValue($item)->getKey() , query: ['relatable_mode' => 1, 'related_column' => $item->{$field->relation()}()->getForeignKeyName(), 'related_key' => $item->getKey()]) }}" class="inline-flex  items-center bg-transparent hover:bg-purple text-purple border border-purple hover:text-white hover:border-transparent font-semibold  py-2 px-4 rounded">
            @include("moonshine::shared.icons.edit", ["size" => 4, "class" => "mr-2"])
            <span>{{ trans('moonshine::ui.edit') }}</span>
        </x-moonshine::async-modal>
    @elseif($item->exists)
        <x-moonshine::async-modal id="create_modal_{{ $field->id() }}" route="{{ $field->resource()->route('create', query: ['relatable_mode' => 1, 'related_column' => $item->{$field->relation()}()->getForeignKeyName(), 'related_key' => $item->getKey()]) }}" class="inline-flex  items-center bg-transparent hover:bg-purple text-purple border border-purple hover:text-white hover:border-transparent font-semibold  py-2 px-4 rounded">
            @include("moonshine::shared.icons.add", ["size" => 4, "class" => "mr-2"])
            <span>{{ trans('moonshine::ui.create') }}</span>
        </x-moonshine::async-modal>
    @endif
@else
    @include('moonshine::fields.shared.'.($field->isFullPage() ? 'full' : 'table').'-fields', [
        'field' => $field,
        'resource' => $resource,
        'item' => $item,
        'model' => $field->formViewValue($item) ?? $field->getRelated($item),
        'level' => $level ?? 0
    ])
@endif
