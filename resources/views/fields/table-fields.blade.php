<x-moonshine::table
    x-data="tableFields({{
    $element->attributes()->get('x-model-has-fields')
            ? 'item.'.$element->field()
            : json_encode($element->jsonValues($item))
    }})"
    data-empty="{{ json_encode($element->jsonValues()) }}"
>
    <x-slot:thead>
        @if(!$element->isFullPage())
            @if(!$toOne)
                <th width="5%" class="text-center">#</th>
            @endif

            @foreach($element->getFields() as $subField)
                <th>{{ $subField->label() }}</th>
            @endforeach

            @if($element->isRemovable())
                <th width="5%" class="text-center"></th>
            @endif
        @endif
    </x-slot:thead>

    <x-slot:tbody>
        <template
            x-for="(item, index{{ $level }}) in items"
            :key="key(item, index{{ $level }})"
        >
            <tr :data-id="key(item, index{{ $level }})" class="table_fields_{{ $element->id() }}">
                @if(!$element->isFullPage())
                    @if(!$toOne)
                        <td class="text-center" scope="row" x-text="index{{ $level }} + 1"></td>
                    @endif

                    @foreach($element->getFields() as $subField)
                        <td class="space-y-3">
                            {{ $resource->renderComponent($subField, $model, $level+1) }}
                        </td>
                    @endforeach

                    @if($element->isRemovable())
                        <td>
                            <button @click.prevent="remove(index{{ $level }})" class="badge badge-red">&times;</button>
                        </td>
                    @endif
                @else
                    @if(!$toOne)
                        <th width="5%" class="text-center" x-text="index{{ $level }} + 1"></th>
                    @endif

                    <td class="space-y-3">
                        @foreach($element->getFields() as $subField)
                            <x-moonshine::field-container :field="$subField" :item="$model" :resource="$resource">
                                {{ $resource->renderComponent($subField, $model, $level+1) }}
                            </x-moonshine::field-container>
                        @endforeach
                    </td>

                    @if($element->isRemovable())
                        <td width="5%" class="text-center">
                            <button @click.prevent="remove(index{{ $level }})" class="badge badge-red">&times;</button>
                        </td>
                    @endif
                @endif
            </tr>
        </template>
    </x-slot:tbody>

    <x-slot:tfoot>
        <td colspan="{{ count($element->getFields())+2 }}">
            <x-moonshine::link
                class="w-full"
                icon="heroicons.plus-circle"
                :x-show="$toOne ? 'items.length == 0' : 'true'"
                @click.prevent="add()"
            >
                @lang('moonshine::ui.' . ($toOne ? 'create' : 'add'))
            </x-moonshine::link>
        </td>
    </x-slot:tfoot>
</x-moonshine::table>
