@if($element->isResourceMode())
    @if($item->exists)
        <x-moonshine::title class="mb-6">
            {{ $element->label() }}
        </x-moonshine::title>

        @if($element->resource()->can('create') && in_array('create', $element->resource()->getActiveActions()))
            <x-moonshine::async-modal
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

        <hr class="divider" />

        <div x-data="{ id: $id('has_many') }">
            <div x-data="asyncData"
                 x-init="load(
                    '{{ $resource->route('relation-field-items', $item->getKey(), query: [
                            '_field_relation' => $element->relation()
                        ]) }}',
                     id
                 )">
                <div :id="id"></div>
            </div>
        </div>
    @endif
@else
    @include('moonshine::fields.table-fields', [
        'element' => $element,
        'resource' => $resource,
        'item' => $item,
        'model' => $element->value()->first() ?? $element->getRelated($item),
        'level' => $level ?? 0,
        'toOne' => false,
    ])
@endif
