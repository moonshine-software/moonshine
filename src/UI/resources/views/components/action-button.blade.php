@props([
    'inDropdown' => false,
    'hasComponent' => false,
    'url' => 'javascript:void(0);',
    'icon' => '',
    'label' => '',
    'component' => null,
    'badge' => false,
    'raw' => false,
    'labelRaw' => false,
    'escapeUi' => false,
])
@if($attributes->has('type'))
    <x-moonshine::form.button
        :attributes="$attributes"
        :raw="$raw"
    >
        {!! $slot !!}

        <x-slot:icon>{!! $icon !!}</x-slot:icon>

        @if(! $escapeUi || $labelRaw)
            {!! $label !!}
        @else
            {{ $label }}
        @endif

        @if($badge !== false)
            <x-moonshine::badge color="">{{ $badge }}</x-moonshine::badge>
        @endif
    </x-moonshine::form.button>
@else
    <x-moonshine::link-button
        :attributes="$attributes"
        :href="$url"
        :badge="$badge"
        :raw="$raw"
    >
        {!! $slot !!}

        <x-slot:icon>{!! $icon !!}</x-slot:icon>

        @if(! $escapeUi || $labelRaw)
            {!! $label !!}
        @else
            {{ $label }}
        @endif
    </x-moonshine::link-button>
@endif

@if($hasComponent)
    <template x-teleport="body">
        {!! $component !!}
    </template>
@endif

