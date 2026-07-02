@props([
    'components' => [],
    'adaptiveColSpan' => 12,
    'colSpan' => 12,
])
<div
    {{ $attributes->class(["col-span-$adaptiveColSpan", "xl:col-span-$colSpan", "space-elements"]) }}
>
    @foreach($components as $component)
        {!! $component !!}
    @endforeach

    {{ $slot ?? '' }}
</div>
