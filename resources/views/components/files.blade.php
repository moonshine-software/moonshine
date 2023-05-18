@props([
    'files' => [],
    'download' => true,
])
@if(array_filter((array) $files))
    <div {{ $attributes->class(['dropzone-items']) }}>
        @foreach($files as $file)
            <div class="dropzone-item dropzone-item-file">
                @include('moonshine::ui.file', [
                    'value' => $file,
                    'download' => $download
                ])
            </div>
        @endforeach
    </div>
@endif
