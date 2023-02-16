<div class="overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
    {!! $resource->extensions('tabs', $item) !!}

    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full">
            <tbody class="bg-white dark:bg-darkblue text-black dark:text-white">
                @foreach($resource->showFields() as $index => $field)
                    <tr class="border-b last:border-0 border-whiteblue dark:border-dark">
                        <td class="px-6 py-4 leading-5 whitespace-nowrap">{{ $field->label() }}:</td>
                        <td class="px-6 w-full py-4">{!! $field->indexViewValue($item) !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(!$resource->isPreviewMode())
        <div class="px-6 py-4">
            @include("moonshine::base.show.shared.actions", ["item" => $item, "resource" => $resource])
        </div>
    @endif
</div>
