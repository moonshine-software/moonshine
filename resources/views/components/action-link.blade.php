@props([
    'action',
    'attributes'
])

@if($action->inDropdown())
    <x-moonshine::link-native
        :attributes="$attributes"
        @class(['p-2' => $action->inDropdown()])
        :href="$action->url()"
        :icon="$action->iconValue()"
    >
        {{ $action->label() }}
    </x-moonshine::link-native>
@else
    <x-moonshine::link-button
        :attributes="$attributes"
        @class(['p-2' => $action->inDropdown()])
        :href="$action->url()"
        :icon="$action->iconValue()"
    >
        {{ $action->label() }}
    </x-moonshine::link-button>
@endif
