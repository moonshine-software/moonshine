@if(session()->has('alert'))
    <x-moonshine::alert :removable="true" type="info">
        {{ session()->get('alert') }}
    </x-moonshine::alert>
@endif

@if($toast = session()->get('toast'))
    <x-moonshine::toast :type="$toast['type']" :content="$toast['message']" />
@endif
