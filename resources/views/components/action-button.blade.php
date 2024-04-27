@props([
    'inDropdown' => false,
    'hasComponent' => false,
    'url' => '#',
    'icon' => '',
    'label' => '',
    'component' => null
])
@if($inDropdown)
    <x-moonshine::link-native class="p-2"
        :attributes="$attributes"
        :href="$url"
        :icon="$icon"
    >
        {{ $label }}
    </x-moonshine::link-native>
@else
    <x-moonshine::link-button
        :attributes="$attributes"
        :href="$url"
        :icon="$icon"
    >
        {{ $label }}
    </x-moonshine::link-button>
@endif

@if($hasComponent)
    {!! $component !!}
@endif

