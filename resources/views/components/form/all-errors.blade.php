@props([
    'errors' => []
])
@if ($errors !== [])
    @foreach ($errors as $error)
        @foreach($error as $message)
            <x-moonshine::alert :removable="false" type="error">
                {{ $message }}
            </x-moonshine::alert>
        @endforeach
    @endforeach
@endif
