@props([
    'control',
    'rowVariable' => 'row',
    'fieldsExpression' => 'fields',
])

@php
    $column = $control['column'];
    $fieldExpression = "fieldByColumn(" . \Illuminate\Support\Js::from($column) . ", {$fieldsExpression})";
    $nestedRowsExpression = "nestedRows({$rowVariable}, {$fieldExpression})";
@endphp

@if(($control['type'] ?? null) === 'json')
    <div @class(['form-group moonshine-field json-field__control', $control['wrapperClass'] ?? ''])>
        <x-moonshine::form.label>
            {{ $control['label'] }}
        </x-moonshine::form.label>

        <div class="json-field json-field--nested">
            <div class="json-field__rows">
                <template x-if="{{ $nestedRowsExpression }}.length === 0">
                    <div class="json-field__ghost flex min-h-16 items-center justify-center px-4 py-3 text-center text-sm text-slate-500">
                        {{ $control['emptyMessage'] ?? '' }}
                    </div>
                </template>

                <template x-for="(nestedRow, nestedRowIndex) in {{ $rowVariable }}[{{ \Illuminate\Support\Js::from($column) }}]" :key="nestedRow._key">
                    <div class="json-field__item">
                        <div
                            class="json-field__ghost json-field__drag-ghost"
                            x-cloak
                            x-show="{{ $fieldExpression }}.reorderable && isDraggingRows({{ $nestedRowsExpression }}) && dropIndex === nestedRowIndex"
                            x-bind:style="ghostStyle()"
                        ></div>

                        <div
                            @class([
                                'json-field__row',
                                'json-field__row--reorderable' => ($control['reorderable'] ?? false) === true,
                            ])
                            x-bind:data-json-row-index="nestedRowIndex"
                            x-bind:class="{ 'json-field__row--dragging': isDraggingRows({{ $nestedRowsExpression }}) && draggingIndex === nestedRowIndex }"
                            x-show="! isDraggingRows({{ $nestedRowsExpression }}) || draggingIndex !== nestedRowIndex"
                        >
                            @if(($control['reorderable'] ?? false) === true)
                                <div class="json-field__reorder">
                                    <x-moonshine::form.button
                                        raw
                                        type="button"
                                        class="btn btn-secondary json-field__reorder-button"
                                        x-on:pointerdown.prevent="dragStartNested($event, {{ $rowVariable }}, {{ $fieldExpression }}, nestedRowIndex)"
                                    >
                                        <x-moonshine::icon icon="bars-3-bottom-right" />
                                    </x-moonshine::form.button>
                                </div>
                            @endif

                            <div @class([
                                'json-field__controls',
                                'json-field__controls--vertical' => ($control['orientation'] ?? 'horizontal') === 'vertical',
                            ])>
                                @foreach($control['controls'] ?? [] as $nestedControl)
                                    @include('moonshine::components.json.control', [
                                        'control' => $nestedControl,
                                        'rowVariable' => 'nestedRow',
                                        'fieldsExpression' => "{$fieldExpression}.fields",
                                    ])
                                @endforeach
                            </div>

                            <div class="json-field__actions">
                                @if($control['buttons'] ?? null)
                                    {!! str_replace(
                                        '__moonshine_json_remove__',
                                        "removeNested({$rowVariable}, {$fieldExpression}, nestedRowIndex)",
                                        $control['buttons'],
                                    ) !!}
                                @elseif(($control['removeButton'] ?? null) && ($control['removable'] ?? true))
                                    {!! str_replace(
                                        '__moonshine_json_remove__',
                                        "removeNested({$rowVariable}, {$fieldExpression}, nestedRowIndex)",
                                        $control['removeButton'],
                                    ) !!}
                                @else
                                    @php
                                        $removeButtonAttributes = $control['removeButtonAttributes'] ?? [];
                                        $hasCustomRemoveClick = array_key_exists('@click.prevent', $removeButtonAttributes)
                                            || array_key_exists('x-on:click.prevent', $removeButtonAttributes);
                                        $removeAttributes = new \Illuminate\View\ComponentAttributeBag(array_merge(
                                            $hasCustomRemoveClick ? [] : ['x-on:click.prevent' => "removeNested({$rowVariable}, {$fieldExpression}, nestedRowIndex)"],
                                            $removeButtonAttributes,
                                        ));
                                    @endphp

                                    <x-moonshine::form.button
                                        raw
                                        type="button"
                                        class="btn btn-error json-field__remove"
                                        x-show="{{ $fieldExpression }}.removable"
                                        :attributes="$removeAttributes"
                                    >
                                        <x-moonshine::icon icon="trash" />
                                    </x-moonshine::form.button>
                                @endif
                            </div>
                        </div>
                    </div>
                </template>

                <div
                    class="json-field__ghost json-field__drag-ghost"
                    x-cloak
                    x-show="{{ $fieldExpression }}.reorderable && isDraggingRows({{ $nestedRowsExpression }}) && dropIndex === {{ $nestedRowsExpression }}.length"
                    x-bind:style="ghostStyle()"
                ></div>
            </div>

            @if(($control['creatable'] ?? true) === true && ($control['hideCreateButton'] ?? false) === false)
                @php
                    $nestedAddExpression = "addNested({$rowVariable}, {$fieldExpression})";
                    $nestedDisabledExpression = "! canAdd({$nestedRowsExpression}, {$fieldExpression})";
                    $createButton = isset($control['createButton'])
                        ? str_replace(
                            ['__moonshine_json_add__', '__moonshine_json_disabled__'],
                            [$nestedAddExpression, $nestedDisabledExpression],
                            $control['createButton'],
                        )
                        : null;
                @endphp

                @if($createButton)
                    {!! $createButton !!}
                @else
                    <x-moonshine::form.button
                        raw
                        type="button"
                        class="btn btn-primary json-field__add"
                        x-on:click.prevent="{{ $nestedAddExpression }}"
                        x-bind:disabled="{{ $nestedDisabledExpression }}"
                    >
                        <x-moonshine::icon icon="plus" />
                    </x-moonshine::form.button>
                @endif
            @endif
        </div>
    </div>
@else
    {!! $control['html'] ?? '' !!}
@endif
