@if(session()->has('alert'))
    <x-moonshine::alert :removable="true">
        {{ session()->get('alert') }}
    </x-moonshine::alert>
@endif
