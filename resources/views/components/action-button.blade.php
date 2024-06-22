@props([
    'inDropdown' => false,
    'hasComponent' => false,
    'url' => '#',
    'icon' => '',
    'label' => '',
    'component' => null,
    'badge' => false,
])
<x-moonshine::link-button
    :attributes="$attributes"
    :href="$url"
    :badge="$badge"
>
    <x-slot:icon>{!! $icon !!}</x-slot:icon>

    {!! $label !!}
</x-moonshine::link-button>

@if($hasComponent)
    {!! $component !!}
@endif

