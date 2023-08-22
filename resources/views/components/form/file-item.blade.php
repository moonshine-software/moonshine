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

    $hiddenName = str($attributes->get('data-name'))
        ->replaceLast($attributes->get('data-column'), 'hidden_' . $attributes->get('data-column'))
        ->value();
@endphp
<div
    class="x-removeable dropzone-item zoom-in @if(!$imageable) dropzone-item-file @endif"
>
    <x-moonshine::form.input
        type="hidden"
        :name="'hidden_' . $attributes->get('name')"
        :attributes="$attributes->only(['data-level'])->merge(['data-name' => $hiddenName])"
        :value="$file"
    />

    @if(!$imageable)
        @include('moonshine::ui.file', [
            'value' => $fileWithDir,
            'download' => $download
        ])
    @endif

    @if($removable)
        <button
            class="dropzone-remove"
            type="button"
            @click.prevent="$event.target.closest('.x-removeable').remove()"
        >
            <x-moonshine::icon
                icon="heroicons.x-mark"
            />
        </button>
    @endif

    @if($imageable)
        @include('moonshine::ui.image', [
            'value' => $fileWithDir,
        ])
    @endif
</div>
