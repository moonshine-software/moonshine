@props([
    'components' => []
])
@foreach($components as $component)
    {!! $component !!}
@endforeach
