@props([
    'href' => 'javascript:void(0);',
    'value' => null,
    'blank' => false,
    'withoutIcon' => false,
    'icon' => 'link',
])
<a href="{{ $href }}"
   {{ $attributes->merge([
        'class' => 'inline-flex items-center gap-1 max-w-full',
   ]) }}
    @if($blank) target="_blank" @endif
>
    @if(!$withoutIcon && $icon)
        <x-moonshine::icon
            class="shrink-0"
            :icon="$icon"
        />
    @endif

    <div class="text-ellipsis overflow-hidden">
        {!! $value ?? $slot !!}
    </div>
</a>
