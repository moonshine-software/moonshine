@if($field->isResourceMode())
    @if($item->exists)
        <div x-data="asyncData" x-init="load('{{ $field->resource()->route('index') }}?related_column={{ $item->{$field->relation()}()->getForeignKeyName() }}&related_key={{ $item->getKey() }}', 'has_many_{{ $field->id() }}')">
            <div id="has_many_{{ $field->id() }}"></div>
        </div>
    @endif
@else
    @include('moonshine::fields.'.($field->isFullPage() ? 'full' : 'table').'-fields', [
        'field' => $field,
        'resource' => $resource,
        'item' => $item,
        'model' => $field->formViewValue($item)->first() ?? $field->getRelated($item),
        'level' => $level ?? 0
    ])
@endif
