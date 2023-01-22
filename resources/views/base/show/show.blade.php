<div class="w-full">
    {!! $resource->extensions('tabs', $item) !!}

    @if($item->exists)
        @foreach($resource->showFields() as $index => $field)
            <div class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm leading-5">{!! $field->indexViewValue($item) !!}</div>
            </div>
        @endforeach

        @if(!$resource->isPreviewMode())
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm leading-5 font-medium">
                @include("moonshine::base.index.shared.item_actions", ["item" => $item, "resource" => $resource])
            </td>
        @endif
    @endif
</div>
