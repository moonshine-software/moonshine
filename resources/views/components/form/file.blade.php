@props([
    'files' => [],
    'path' => '',
    'download' => false,
    'removable' => true,
    'imageable' => true
])
<div class="form-group-dropzone">
    <div class="dropzone">
        <div class="dropzone-message">
            <button class="dropzone-button" type="button">
                <x-moonshine::icon
                    icon="heroicons.cloud-arrow-up"
                    size="6"
                />

                Drop files here to upload
            </button>

            <x-moonshine::form.input type="file" {{ $attributes }} />
        </div>
    </div>

    <!-- Files uploaded -->
    <div class="dropzone">
        <div class="dropzone-items">
            @foreach($files as $file)
                <div id="hidden_parent_{{ $attributes->get('id')  }}"
                     class="dropzone-item zoom-in @if(!$imageable) dropzone-item-file @endif"
                >

                    <x-moonshine::form.input
                        type="hidden"
                        x-ref="hidden_{{ $attributes->get('id') }}"
                    />

                    @if(!$imageable)
                        @include('moonshine::ui.file', [
                            'file' => $file,
                            'download' => $download
                        ])
                    @endif

                    @if($removable)
                        <button
                            class="dropzone-remove"
                            @click.prevent="$event.target.closest('#hidden_parent_{{ $attributes->get('id')  }}' + index).remove()"
                        >
                            <x-moonshine::icon icon="heroicons.x-mark"/>
                        </button>
                    @endif

                    @if($imageable)
                        <img
                            @if($attributes->has('x-model'))
                                :src="imageValue ? ('{{ $path }}') + imageValue : ''"
                            @else
                                src="{{ $path }}{{ $file }}"
                            @endif
                        >
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
