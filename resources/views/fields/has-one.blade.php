@if($element->isResourceMode())
    <div class="mb-6">
        <x-moonshine::title class="mb-6">
            {{ $element->label() }}
        </x-moonshine::title>

        @if($element->formViewValue($item)
            && $element->resource()->can('update')
            && in_array('edit', $element->resource()->getActiveActions())
        )
            <x-moonshine::async-modal
                id="edit_{{ $item->getTable() }}_modal_{{ $element->id() }}_{{ $item->getKey() }}"
                route="{{ $element->resource()->relatable()->route('edit', $element->formViewValue($item)->getKey()) }}"
                title="{{ $element->resource()->title() }}"
            >
                <x-moonshine::icon
                    icon="heroicons.pencil-square"
                    size="4"
                />
                <span>{{ trans('moonshine::ui.edit') }}</span>
            </x-moonshine::async-modal>
        @elseif($item->exists
            && $element->resource()->can('create')
            && in_array('create', $element->resource()->getActiveActions())
        )
            <x-moonshine::async-modal
                id="create_{{ $element->resource()->getModel()->getTable() }}_modal_{{ $element->id() }}"
                route="{{ $resource->route('relation-field-form', query: [
                '_field_relation' => $element->relation(),
                '_related_key' => $item->getKey()
                ]) }}"
                title="{{ $element->resource()->title() }}"
            >
                <x-moonshine::icon
                    icon="heroicons.squares-plus"
                    size="4"
                />
                <span>{{ trans('moonshine::ui.create') }}</span>
            </x-moonshine::async-modal>
        @endif
    </div>
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
