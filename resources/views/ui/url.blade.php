<a href="{{ $href ?? '#' }}" class="inline-flex items-center gap-1" @if(!empty($blank)) target="_blank" @endif>
    <x-moonshine::icon icon="heroicons.link" />

    {{ $value ?? null }}
</a>
