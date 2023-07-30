@if($action->isConfirmed())
    @include('moonshine::crud.shared.confirm-action', [
        'action' => $action,
        'url' => $action->url()
    ])
@else
    <x-dynamic-component
        :attributes="$action->attributes()"
        :component="'moonshine::' . ($action->inDropdown() ? 'link-native' : 'link')"
        @class(['p-2' => $action->inDropdown()])
        :href="$action->url()"
        :icon="$action->iconValue()"
    >
        {{ $action->label() }}
    </x-dynamic-component>
@endif

