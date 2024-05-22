@props([
    'files' => [],
    'download' => true,
])
@if($files !== [])
    <div {{ $attributes->class(['dropzone-items']) }}>
        @foreach($files as $index => $file)
            <div
                {{ $file['attributes']?->class(['dropzone-item dropzone-item-file']) }}
            >
                <x-moonshine::file
                    :value="$file['full_path']"
                    :filename="$file['name']"
                    :download="$download"
                />
            </div>
        @endforeach
    </div>
@endif
