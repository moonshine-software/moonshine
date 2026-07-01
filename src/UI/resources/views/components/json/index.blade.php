@props([
    'rows' => [],
    'fields' => [],
    'controls' => [],
    'inputName' => null,
    'removable' => true,
    'creatable' => true,
    'creatableLimit' => null,
    'hideCreateButton' => false,
    'createButton' => null,
    'buttons' => null,
    'removeButton' => null,
    'removeButtonAttributes' => [],
    'reorderable' => false,
    'orientation' => 'horizontal',
    'keyValue' => false,
    'onlyValue' => false,
    'objectMode' => false,
    'filterEmpty' => true,
    'emptyMessage' => '',
])

<div
    x-data="jsonTreeField(@js($rows), @js($fields), @js($removable), @js($reorderable), @js($keyValue), @js($onlyValue), @js($objectMode), @js($filterEmpty), @js($creatable), @js($creatableLimit))"
    {{ $attributes->only('class')->class('json-field') }}
    data-show-when-field="{{ $attributes->get('data-show-when-field', $inputName) }}"
>
    <x-moonshine::form.input
        type="hidden"
        name="{{ $inputName }}"
        id="{{ $attributes->get('id') }}"
        x-model="payload"
    />

    <div class="json-field__rows">
        <template x-if="rows.length === 0">
            <div class="json-field__ghost flex min-h-16 items-center justify-center px-4 py-3 text-center text-sm text-slate-500">
                {{ $emptyMessage }}
            </div>
        </template>

        <template x-for="(row, rowIndex) in rows" :key="row._key">
            <div
                class="json-field__item"
            >
                <div
                    class="json-field__ghost json-field__drag-ghost"
                    x-cloak
                    x-show="reorderable && isDraggingRows(rows) && dropIndex === rowIndex"
                    x-bind:style="ghostStyle()"
                ></div>

                <div
                    @class([
                        'json-field__row',
                        'json-field__row--reorderable' => $reorderable,
                    ])
                    x-bind:data-json-row-index="rowIndex"
                    x-bind:class="{ 'json-field__row--dragging': isDraggingRows(rows) && draggingIndex === rowIndex }"
                    x-show="! isDraggingRows(rows) || draggingIndex !== rowIndex"
                >
                    @if($reorderable)
                        <div class="json-field__reorder">
                            <x-moonshine::form.button
                                raw
                                type="button"
                                class="btn btn-secondary json-field__reorder-button"
                                x-on:pointerdown.prevent="dragStart($event, rowIndex)"
                            >
                                <x-moonshine::icon icon="bars-3-bottom-right" />
                            </x-moonshine::form.button>
                        </div>
                    @endif

                    <div @class([
                        'json-field__controls',
                        'json-field__controls--vertical' => $orientation === 'vertical',
                    ])>
                        @foreach($controls as $control)
                            @include('moonshine::components.json.control', [
                                'control' => $control,
                                'rowVariable' => 'row',
                                'fieldsExpression' => 'fields',
                            ])
                        @endforeach
                    </div>

                    <div class="json-field__actions">
                        @if($buttons)
                            {!! $buttons !!}
                        @elseif($removeButton && $removable)
                            {!! $removeButton !!}
                        @elseif($removable)
                            @php
                                $hasCustomRemoveClick = array_key_exists('@click.prevent', $removeButtonAttributes)
                                    || array_key_exists('x-on:click.prevent', $removeButtonAttributes);
                                $removeAttributes = new \Illuminate\View\ComponentAttributeBag(array_merge(
                                    $hasCustomRemoveClick ? [] : ['x-on:click.prevent' => 'remove(rowIndex)'],
                                    $removeButtonAttributes,
                                ));
                            @endphp

                            <x-moonshine::form.button
                                raw
                                type="button"
                                class="btn btn-error json-field__remove"
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
            x-show="reorderable && isDraggingRows(rows) && dropIndex === rows.length"
            x-bind:style="ghostStyle()"
        ></div>
    </div>

    @if($creatable && ! $hideCreateButton)
        @if($createButton)
            {!! $createButton !!}
        @else
            <x-moonshine::form.button
                raw
                type="button"
                class="btn btn-primary json-field__add"
                x-on:click.prevent="add()"
                x-bind:disabled="! canAdd()"
            >
                <x-moonshine::icon icon="plus" />
            </x-moonshine::form.button>
        @endif
    @endif
</div>
