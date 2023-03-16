<x-moonshine::box
        title="#{{ $item->getKey() }}"
>
    @foreach($resource->showFields() as $index => $field)
        <div class="">
            @include('moonshine::ui.badge', [
                'value' => $field->label(),
                'color' => 'purple'
            ])
            <span>{!! $field->indexViewValue($item) !!}</span>
        </div>
    @endforeach
</x-moonshine::box>


@if(!$resource->isPreviewMode())
    @include("moonshine::crud.shared.detail-card-actions", ["item" => $item, "resource" => $resource])
@endif
