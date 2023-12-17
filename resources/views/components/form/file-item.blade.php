@props([
    'file' => null,
    'raw' => null,
    'download' => false,
    'removable' => true,
    'removableAttributes' => null,
    'imageable' => true
])
<div
    class="x-removeable dropzone-item zoom-in @if(!$imageable) dropzone-item-file @endif"
>
    <x-moonshine::form.input
        type="hidden"
        :name="'hidden_' . $attributes->get('name')"
        :attributes="$attributes->only(['data-level'])->merge([
            'data-name' => str($attributes->get('data-name'))
                ->replaceLast($attributes->get('data-column'), 'hidden_' . $attributes->get('data-column'))
                ->value()
        ])"
        :value="$raw"
    />

    @if(!$imageable)
        @include('moonshine::ui.file', [
            'value' => $file,
            'download' => $download
        ])
    @endif

    @if($removable)
        <button
            {{ $removableAttributes?->merge([
                '@click.prevent' => '$event.target.closest(".x-removeable").remove()',
                'type' => 'button',
                'class' => 'dropzone-remove',
            ]) }}
        >
            <x-moonshine::icon
                icon="heroicons.x-mark"
            />
        </button>
    @endif

    @if($imageable)
        <img src="{{ $file }}"
             @click.stop="$dispatch('img-popup', {open: true, src: '{{ $file }}' })"
             alt=""
        />
    @endif
</div>
