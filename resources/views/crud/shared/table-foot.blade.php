@if($resource->hasMassAction())
    <td colspan="{{ count($resource->getFields()->indexFields())+2 }}"
    >
        <div class="flex items-center gap-4">
            @include('moonshine::crud.shared.bulk-actions', ['resource' => $resource])
        </div>
    </td>
@endif
