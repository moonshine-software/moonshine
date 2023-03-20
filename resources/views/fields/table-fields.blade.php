<x-moonshine::table
    x-data="handler_{{ $field->id() }}()"
    x-init="handler_init_{{ $field->id() }}"
>
    <x-slot:thead>
        @if(!$field->toOne() && !$field->isFullPage())
            <th width="5%" class="text-center">#</th>

            @foreach($field->getFields() as $subField)
                <th>{{ $subField->label() }}</th>
            @endforeach

            @if($field->isRemovable())
              <th width="5%" class="text-center"></th>
            @endif
        @endif
    </x-slot:thead>

    <x-slot:tbody>
        <template
            x-for="(item, index{{ $level }}) in items"
            :key="Object.values(item)[0] ? (index{{ $level }} + '' + Object.values(item)[0]) : index{{ $level }}"
        >
            <tr :data-id="item.id" class="table_fields_{{ $field->id() }}">
                @if(!$field->toOne() && !$field->isFullPage())
                    <td class="text-center" scope="row" x-text="index{{ $level }} + 1"></td>

                    @foreach($field->getFields() as $subField)
                        <td class="space-y-3">
                            {{ $resource->renderField($subField, $model, $level+1) }}
                        </td>
                    @endforeach

                    <td>
                        @if($field->isRemovable())
                            <button @click.prevent="removeField(index{{ $level }})" class="badge badge-red">&times;</button>
                        @endif
                    </td>
                @else
                    <th width="5%" class="text-center" x-text="index{{ $level }} + 1"></th>

                    <td class="space-y-3">
                        @foreach($field->getFields() as $subField)
                            <x-moonshine::field-container :field="$subField" :item="$model" :resource="$resource">
                                {{ $resource->renderField($subField, $model, $level+1) }}
                            </x-moonshine::field-container>
                        @endforeach
                    </td>

                    @if($field->isRemovable() && !$field->toOne())
                        <td width="5%" class="text-center">
                            <button @click.prevent="removeField(index{{ $level }})" class="badge badge-red">&times;</button>
                        </td>
                    @endif
                @endif
            </tr>
        </template>
    </x-slot:tbody>

    <x-slot:tfoot>
        <td colspan="{{ count($field->getFields())+2 }}">
            <x-moonshine::link
                href="#"
                class="w-full"
                icon="heroicons.plus-circle"
                :x-show="$field->toOne() ? 'items.length == 0' : 'true'"
                @click.prevent="addNewField()"
            >
                @lang('moonshine::ui.' . ($field->toOne() ? 'create' : 'add'))
            </x-moonshine::link>
        </td>
    </x-slot:tfoot>
</x-moonshine::table>

<script>
    function handler_{{ $field->id() }}() {
        return {
            handler_init_{{ $field->id() }}() {
                this.items = @json($field->jsonValues($item));
            },
            items: [],
            addNewField() {
                if(Array.isArray(this.items)) {
                    this.items.push(@json($field->jsonValues()));
                } else {
                    this.items = [@json($field->jsonValues())];
                }
            },
            removeField(index) {
                this.items.splice(index, 1);
            },
        }
    }
</script>

