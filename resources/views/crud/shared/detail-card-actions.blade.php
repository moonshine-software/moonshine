<div class="col-span-12 lg:col-span-8">
    <div class="mt-3 flex w-full flex-wrap justify-end gap-2">
        @include('moonshine::crud.shared.item-actions', [
            'resource' => $resource,
            'item' => $item,
            'except' => ['show']
        ])
    </div>
</div>
