@props([
    'files' => [],
    'path' => '',
    'dir' => '',
    'download' => false,
    'removable' => true,
    'imageable' => true
])
<div class="form-group form-group-dropzone">
    <x-moonshine::form.input
        type="file"
        {{ $attributes->merge(['class' => 'form-file-upload'])->except(['id'])}}
    />

    @if(array_filter((array) $files))
        <div class="dropzone">
            <div class="dropzone-items">
                @foreach($files as $index => $file)
                    <x-moonshine::form.file-item
                        :attributes="$attributes"
                        :dir="$dir"
                        :path="$path"
                        :file="$file"
                        :download="$download"
                        :removable="$removable"
                        :imageable="$imageable"
                    />
                @endforeach
            </div>
        </div>
    @endif
</div>
