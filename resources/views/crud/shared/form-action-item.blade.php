@if($action->confirmation())
    @include('moonshine::crud.shared.confirm-action', ['action' => $action, 'href' => $resource->route('actions.form', $item->getKey(), ['index' => $index])])
@else
    <x-moonshine::link-native
        class="p-2"
        :href="$resource->route('actions.form', $item->getKey(), ['index' => $index])"
        :icon="$action->iconValue()"
    >
        {{ $action->label() }}
    </x-moonshine::link-native>
@endif
