<span class="dropzone-file-icon">
    <x-moonshine::icon
        icon="heroicons.document"
        size="6"
    />
</span>
<h5 class="dropzone-file-name">
    @if($xValue ?? false)
        <a @if($download ?? false) download :href="{{ $xValue }}" @endif x-text="{{ $xValue }}"></a>
    @else
        <a @if($download ?? false) download href="{{ $value }}" @endif>{{ $value }}</a>
    @endif
</h5>
