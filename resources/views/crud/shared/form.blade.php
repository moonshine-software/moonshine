<x-moonshine::form :errors="$errors"
    x-data="crudForm()"
    :x-init="'init('.json_encode($resource->getFields()->whenFieldNames()->map(fn($value) => $item[$value] ?? '')).')'"
    action="{{ $resource->route(($item->exists ? 'update' : 'store'), $item->getKey()) }}"
    enctype="multipart/form-data"
    method="POST"
    x-on:submit.prevent="{{ $resource->isPrecognition() ? 'precognition($event.target)' : '$event.target.submit()' }}"
>
    @include('moonshine::crud.shared.form-hidden', ['resource' => $resource])

    @if($item->exists)
        @method('PUT')
    @endif

    <x-moonshine::resource-renderable
        :components="$resource->getFields()->withParents()"
        :item="$item"
        :resource="$resource"
    />

    @include('moonshine::crud.shared.form-actions', [
        'item' => $item,
        'resource' => $resource
    ])
</x-moonshine::form>

@if($item->exists)
    @foreach($resource->getFields()->relatable() as $field)
        @if($field->canDisplayOnForm($item))
            {{ $resource->renderComponent($field, $item) }}
        @endif
    @endforeach
@endif

@if(!empty($resource->components()))
    @foreach($resource->components() as $formComponent)
        @if($formComponent->isSee($item))
            {{ $resource->renderComponent($formComponent, $item) }}
        @endif
    @endforeach
@endif
