<x-moonshine::box
        title="#{{ $item->getKey() }}"
>
    <table class="table">
        @foreach($resource->getFields()->detailFields() as $index => $field)
            <tr>
                <th width="15%">
                    {{$field->label()}}
                </th>
                <td>{!! $field->indexViewValue($item) !!}</td>
            </tr>
        @endforeach
    </table>
</x-moonshine::box>


@if(!$resource->isPreviewMode())
    @include("moonshine::crud.shared.detail-card-actions", ["item" => $item, "resource" => $resource])
@endif
