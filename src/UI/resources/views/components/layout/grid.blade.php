@props([
    'components' => [],
    'gap' => 6,
])

<div {{ $attributes->merge([
    'class' => "grid grid-cols-12 gap-$gap",
]) }}>
    @foreach($components as $component)
        {!! $component !!}
    @endforeach

    {{ $slot ?? '' }}
</div>
