<x-moonshine::table
    x-data="handler_{{ $field->id() }}()"
    x-init="handler_init_{{ $field->id() }}"
>
    <x-slot:thead>
        @if(!$field->toOne() && !$field->isFullPage())
            <th class="w-4 text-center">#</th>

            @foreach($field->getFields() as $subField)
                <th>{{ $subField->label() }}</th>
            @endforeach

            <th class="w-4"></th>
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
                        <td>
                            {{ $resource->renderField($subField, $model, $level+1) }}
                        </td>
                    @endforeach

                    @if(!$field->toOne())
                        <td>
                            @if($field->isRemovable())
                                <button @click.prevent="removeField(index{{ $level }})" class="badge badge-red">
                                    <x-moonshine::icon
                                        icon="heroicons.x-mark"
                                        color="pink"
                                        size="6"
                                    />
                                </button>
                            @endif
                        </td>
                    @endif
                @else
                    <th width="5%" x-text="index{{ $level }} + 1"></th>

                    <td>
                        @foreach($field->getFields() as $subField)
                            {{ $resource->renderField($subField, $model, $level+1) }}
                        @endforeach
                    </td>

                    @if($field->isRemovable())
                        <button @click.prevent="removeField(index{{ $level }})" class="badge badge-red">
                            <x-moonshine::icon
                                icon="heroicons.x-mark"
                                color="pink"
                                size="6"
                            />
                        </button>
                    @endif
                @endif
            </tr>
        </template>
    </x-slot:tbody>

    @if(!$field->toOne())
        <x-slot:tfoot>
            <td colspan="{{ count($field->getFields())+2 }}">
                <x-moonshine::link
                    href="#"
                    class="w-full"
                    icon="heroicons.plus-circle"
                    @click.prevent="addNewField()"
                >
                    @lang('moonshine::ui.add')
                </x-moonshine::link>
            </td>
        </x-slot:tfoot>
    @endif
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

