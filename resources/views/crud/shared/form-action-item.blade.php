@if($action->confirmation())
    @include('moonshine::crud.shared.confirm-action', ['action' => $action, 'href' => $resource->route('actions.form', $item->getKey(), ['index' => $index])])
@else
    <x-dynamic-component
        :component="'moonshine::' . ($action->inDropdown() ? 'link-native' : 'link')"
        @class(['p-2' => $action->inDropdown()])
        :href="$resource->route('actions.form', $item->getKey(), ['index' => $index])"
        :icon="$action->iconValue()"
    >
        {{ $action->label() }}
    </x-dynamic-component>
@endif
