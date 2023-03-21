@props([
    'file' => null,
    'path' => '',
    'download' => false,
    'removable' => true,
    'imageable' => true
])
<div
    id="hidden_parent_{{ $attributes->get('id')  }}"
    class="x-removeable dropzone-item zoom-in @if(!$imageable) dropzone-item-file @endif"
>
    <x-moonshine::form.input
        type="hidden"
        :name="'hidden_' . $attributes->get('name')"
        {{ $attributes->merge(array_merge([
            'x-ref' => 'hidden_' . $attributes->get('id'),
        ], $attributes->has('x-model') ? [
           ':name' => '`hidden_`+' . $attributes->get('x-bind:name'),
           ':value' => 'xValue',
        ] : ['value' => $file]))->except(['id', 'name', 'x-bind:name', 'x-model', 'x-bind:id', 'accept', 'multiple']) }}
    />

    @if(!$imageable)
        @include('moonshine::ui.file', [
            'value' => $file,
            'xValue' => $attributes->has('x-model') ? "xValue ? ('$path') + xValue : ''" : null,
            'download' => $download
        ])
    @endif

    @if($removable)
        <button
            class="dropzone-remove"
            @click.prevent="$event.target.closest('#hidden_parent_{{ $attributes->get('id')  }}').remove()"
        >
            <x-moonshine::icon icon="heroicons.x-mark"/>
        </button>
    @endif

    @if($imageable)
        <img
            @if($attributes->has('x-model'))
                :src="xValue ? ('{{ $path }}') + xValue : ''"
            @else
                src="{{ $path }}{{ $file }}"
            @endif
        >
    @endif
</div>
