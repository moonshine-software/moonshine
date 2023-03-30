<x-moonshine::form :errors="$errors"
    x-data="crudForm()"
    :x-init="'init('.json_encode($resource->getFields()->whenFieldNames()->map(fn($value) => $item[$value] ?? '')).')'"
    action="{{ $resource->route(($item->exists ? 'update' : 'store'), $item->getKey()) }}"
    enctype="multipart/form-data"
    method="POST"
    x-on:submit.prevent="{{ $resource->isPrecognition() ? 'precognition($event.target)' : '$event.target.submit()' }}"
>
    @if(request('relatable_mode'))
        <x-moonshine::form.input
            type="hidden"
            name="relatable_mode"
            value="1"
        />
    @endif

    @if($item->exists)
        @method('PUT')
    @endif

    <x-moonshine::resource-renderable
        :components="$resource->getFields()->withParents()"
        :item="$item"
        :resource="$resource"
    />

    <x-slot:button class="form_submit_button">
        {{ trans('moonshine::ui.save') }}
    </x-slot:button>

    @if($item->exists && !request('relatable_mode'))
        <x-slot:buttons>
            @foreach($resource->formActions() as $index => $action)
                @if($action->isSee($item))
                    <x-moonshine::link
                        :href="$resource->route('actions.form', $item->getKey(), ['index' => $index])"
                        :icon="$action->iconValue()"
                    >
                        {{ $action->label() }}
                    </x-moonshine::link>
                @endif
            @endforeach
        </x-slot:buttons>
    @endif
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
