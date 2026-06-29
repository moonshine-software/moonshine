@props([
    'components' => [],
])
<div {{ $attributes }} >
    @foreach($components as $component)
        {!! $component !!}
    @endforeach

    {{ $slot ?? '' }}
</div>
