@props([
    'icon' => 'bell-alert',
    'type' => 'default',
    'removable' => false
])
<div {{ $attributes->merge(['class' => "alert alert-$type"]) }}
    @if($removable)
    x-init="setTimeout(function() {$refs.alert.remove()}, 2000)"
    x-data
    x-ref="alert"
    @endif
>
    <div class="alert-icon">
        <x-moonshine::icon :icon="$icon" />
    </div>
    <p class="alert-content">{{ $slot ?? '' }}</p>

    @if($removable)
        <a href="javascript:void(0);" @click.prevent="$refs.alert.remove()" class="alert-remove">
            <x-moonshine::icon icon="x-mark" />
        </a>
    @endif
</div>
