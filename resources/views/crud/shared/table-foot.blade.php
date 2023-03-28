@if($resource->hasMassAction())
    <td class="text-center"
        colspan="{{ count($resource->getFields()->indexFields())+2 }}"
    >
        <div class="flex items-center gap-2">
            @include('moonshine::crud.shared.bulk-actions', ['resource' => $resource])
        </div>
    </td>
@endif
