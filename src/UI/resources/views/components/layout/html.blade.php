@props([
    'components' => [],
])
<!DOCTYPE html>
<html {{ $attributes }}>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</html>
