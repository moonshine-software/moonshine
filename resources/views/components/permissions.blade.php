@if($item->exists && $resource->hasUserPermissions())
    <div>
        <div class="text-lg my-4">{{ $label }}</div>

        {{ $form->render() }}
    </div>
@endif
