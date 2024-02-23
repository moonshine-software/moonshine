@props([
    'files' => [],
    'download' => true,
    'names' => null,
    'itemAttributes' => null,
])
@if(array_filter((array) $files))
    <div {{ $attributes->class(['dropzone-items']) }}>
        @foreach($files as $index => $file)
            <div
                @if(is_null($itemAttributes))
                    class="dropzone-item dropzone-item-file"
                @else
                    {{ value($itemAttributes, $file, $index)?->class(['dropzone-item dropzone-item-file']) }}
                @endif
            >
                @include('moonshine::ui.file', [
                    'value' => $file,
                    'filename' => value($names, $file, $index),
                    'download' => $download
                ])
            </div>
        @endforeach
    </div>
@endif
