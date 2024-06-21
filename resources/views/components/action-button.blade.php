@props([
    'inDropdown' => false,
    'hasComponent' => false,
    'url' => '#',
    'icon' => '',
    'label' => '',
    'component' => null
])
<x-moonshine::link-button
    :attributes="$attributes"
    :href="$url"
>
    <x-slot:icon>{!! $icon !!}</x-slot:icon>

    {!! $label !!}
</x-moonshine::link-button>

@if($hasComponent)
    {!! $component !!}
@endif

