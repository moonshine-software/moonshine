@if($element->isResourceMode())
    @if($item->exists)
        <div x-data="asyncData" x-init="load('{{ $element->resource()->route('index') }}?related_column={{ $item->{$element->relation()}()->getForeignKeyName() }}&related_key={{ $item->getKey() }}', 'has_many_{{ $element->id() }}')">
            <div id="has_many_{{ $element->id() }}"></div>
        </div>
    @endif
@else
    @include('moonshine::fields.table-fields', [
        'element' => $element,
        'resource' => $resource,
        'item' => $item,
        'model' => $element->formViewValue($item)->first() ?? $element->getRelated($item),
        'level' => $level ?? 0
    ])
@endif
