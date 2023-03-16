<div class="flex items-center justify-end gap-2">
    @include('moonshine::crud.shared.item-actions', [
        'resource' => $resource,
        'item' => $item,
        'except' => []
    ])
</div>
