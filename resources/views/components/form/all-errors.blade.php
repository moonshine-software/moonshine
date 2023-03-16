@if (isset($errors) && $errors->any())
    @foreach ($errors->all() as $error)
        <x-moonshine::alert :removable="false" type="error">
            {{ $error }}
        </x-moonshine::alert>
    @endforeach
@endif
