@props([
    'components' => []
])
<!DOCTYPE html>
<html {{ $attributes }}
      lang="{{ str_replace('_', '-', app()->getLocale()) }}"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</html>
