@if($action->confirmation())
    @include('moonshine::crud.shared.confirm-action', ['action' => $action, 'href' => $resource->route('actions.item', $item->getKey(), request()->routeIs('*.query-tag') ? ['index' => $index, 'redirect_back' => 1] : ['index' => $index])])
@else
    <x-moonshine::link
        :href="$resource->route('actions.item', $item->getKey(), request()->routeIs('*.query-tag') ? ['index' => $index, 'redirect_back' => 1] : ['index' => $index])"
        :icon="$action->iconValue()"
        :title="$action->label()"
    />
@endif

