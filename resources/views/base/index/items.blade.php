@foreach($items as $item)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap">
            <input class="actionBarCheckboxRow" @change='actionBar("item")' name="items[{{ $item->getKey() }}]"
                   type="checkbox" value="{{ $item->getKey() }}"/>
        </td>

        @foreach($resource->indexFields() as $field)
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm leading-5">{!! $field->indexViewValue($item) !!}</div>
            </td>
        @endforeach

        <td class="px-6 py-4 whitespace-nowrap text-right text-sm leading-5 font-medium">
            @include("moonshine::base.index.shared.item_actions", ["item" => $item, "resource" => $resource])
        </td>
    </tr>
@endforeach
