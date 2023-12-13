@props([
    'components' => []
])
@foreach($components as $component)
    @continue(!isSeeWhenExists($component))

    {{ $component->render() }}
@endforeach
