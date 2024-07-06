@props([
    'alert' => false,
    'toast' => false,
    'type' => 'info',
    'withToast' => true,
    'removable' => true,
])
@if($alert)
    <x-moonshine::alert :removable="$removable" :type="$type">
        {{ $alert }}
    </x-moonshine::alert>
@endif

@if($withToast && $toast)
    <x-moonshine::toast :type="$toast['type']" :content="$toast['message']" />
@endif
