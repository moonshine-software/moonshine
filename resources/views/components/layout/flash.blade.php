@props([
    'key' => 'alert',
    'type' => 'info',
    'withToast' => true,
    'removable' => true,
])
@if(session()->has($key))
    <x-moonshine::alert :removable="$removable" :type="$type">
        {{ session()->get($key) }}
    </x-moonshine::alert>
@endif

@if($withToast && $toast = session()->get('toast'))
    <x-moonshine::toast :type="$toast['type']" :content="$toast['message']" />
@endif
