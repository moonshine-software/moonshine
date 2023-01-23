<div>
    {!! $resource->extensions('tabs', $item) !!}

    @if($item->exists)
        @foreach($resource->showFields() as $index => $field)
            <div class="flex items-center space-x-2 px-3 py-3 whitespace-nowrap">
                <span class="text-sm">{{ $field->label() }}</span>:
                <div class="leading-5">{!! $field->indexViewValue($item) !!}</div>
            </div>
        @endforeach

        @if(!$resource->isPreviewMode())
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm leading-5 font-medium">
                @include("moonshine::base.index.shared.item_actions", ["item" => $item, "resource" => $resource])
            </td>
        @endif
    @endif
</div>
