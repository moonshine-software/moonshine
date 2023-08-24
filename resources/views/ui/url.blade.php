<a href="{{ $href ?? '#' }}" class="inline-flex items-center gap-1" @if(!empty($blank)) target="_blank" @endif>
    @if(($withoutIcon ?? false) === false)
        <x-moonshine::icon
            :icon="$icon ?? 'heroicons.link'"
        />
    @endif

    {{ $value ?? null }}
</a>
