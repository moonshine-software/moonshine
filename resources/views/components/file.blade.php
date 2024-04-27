@props([
    'value' => '',
    'filename' => '',
    'download' => false
])
<span class="dropzone-file-icon">
    <x-moonshine::icon
        icon="heroicons.document"
        size="6"
    />
</span>
<h5 class="dropzone-file-name">
    <a
        @if($download ?? false) download href="{{ $value }}" @endif
    >
        {{ $filename ?? $value }}
    </a>
</h5>
