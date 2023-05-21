@props([
    'file' => null,
    'path' => '',
    'dir' => '',
    'download' => false,
    'removable' => true,
    'imageable' => true
])
@php
    $fileWithDir = str($file)->remove($dir)
                        ->prepend($dir)
                        ->prepend($path)
                        ->value();

    $xValueExpr = "xValue";

    if($path || $dir) {
        $xValueExpr = "xValue && xValue.length ? ('$path') + '$dir' + (!xValue.replace('$dir', '').startsWith('/') ? '/' : '') + xValue.replace('$dir', '') : ''";
    }
@endphp

<div
    id="hidden_parent_{{ $attributes->get('id')  }}"
    class="x-removeable dropzone-item zoom-in @if(!$imageable) dropzone-item-file @endif"
>
    <x-moonshine::form.input
        type="hidden"
        :name="'hidden_' . $attributes->get('name')"
        {{ $attributes->merge(array_merge([
            'x-ref' => 'hidden_' . $attributes->get('id'),
        ], $attributes->has('x-model-field') ? [
           ':name' => '`hidden_`+' . $attributes->get('x-bind:name'),
           ':value' => 'xValue',
        ] : ['value' => $file]))->except(['type', 'id', 'name', 'x-bind:name', 'x-model', 'x-model.lazy', 'x-bind:id', 'accept', 'multiple']) }}
    />

    @if(!$imageable)
        @include('moonshine::ui.file', [
            'value' => $fileWithDir,
            'xValue' => $attributes->has('x-model-field')
                ? $xValueExpr
                : null,
            'download' => $download
        ])
    @endif

    @if($removable)
        <button
            class="dropzone-remove"
            type="button"
            @click.prevent="$event.target.closest('#hidden_parent_{{ $attributes->get('id')  }}').remove()"
        >
            <x-moonshine::icon icon="heroicons.x-mark"/>
        </button>
    @endif

    @if($imageable)
        <img
            @if($attributes->has('x-model-field'))
                :src="{{ $xValueExpr }}"
            @else
                src="{{ $fileWithDir }}"
            @endif
        >
    @endif
</div>
