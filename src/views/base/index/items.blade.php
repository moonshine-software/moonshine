@foreach($resource->paginate() as $item)
    <tr>
        <td class="px-6 py-4 whitespace-no-wrap">
            <input class="actionBarCheckboxRow" @change='actionBar("item")' name="items[{{ $item->id }}]" type="checkbox" value="{{ $item->id }}" />
        </td>

        @foreach($resource->indexFields() as $field)
            <td class="px-6 py-4 whitespace-no-wrap">
                <div class="text-sm leading-5 text-gray-900">{!! $field->indexViewValue($item) !!}</div>
            </td>
        @endforeach

        <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
            @include("moonshine::base.index.shared.item_actions", ["item" => $item, "resource" => $resource])
        </td>
    </tr>
@endforeach
