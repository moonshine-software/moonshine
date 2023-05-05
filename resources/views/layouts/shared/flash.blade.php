@if(session()->has('alert'))
    <x-moonshine::alert :removable="true" type="info">
        {{ session()->get('alert') }}
    </x-moonshine::alert>
@endif
