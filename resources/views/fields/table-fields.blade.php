<x-moonshine::table
    x-data="handler_{{ $element->id() }}()"
    x-init="handler_init_{{ $element->id() }}"
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
            :key="Object.values(item)[0] ? (index{{ $level }} + '' + Object.values(item)[0]) : index{{ $level }}"
        >
            <tr :data-id="item.id" class="table_fields_{{ $element->id() }}">
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
                            <button @click.prevent="removeField(index{{ $level }})" class="badge badge-red">&times;</button>
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
                            <button @click.prevent="removeField(index{{ $level }})" class="badge badge-red">&times;</button>
                        </td>
                    @endif
                @endif
            </tr>
        </template>
    </x-slot:tbody>

    <x-slot:tfoot>
        <td colspan="{{ count($element->getFields())+2 }}">
            <x-moonshine::link
                href="#"
                class="w-full"
                icon="heroicons.plus-circle"
                :x-show="$toOne ? 'items.length == 0' : 'true'"
                @click.prevent="addNewField()"
            >
                @lang('moonshine::ui.' . ($toOne ? 'create' : 'add'))
            </x-moonshine::link>
        </td>
    </x-slot:tfoot>
</x-moonshine::table>

<script>
    function handler_{{ $element->id() }}() {
        return {
            handler_init_{{ $element->id() }}() {
                this.items = @json($element->jsonValues($item));
            },
            items: [],
            addNewField() {
                if(Array.isArray(this.items)) {
                    this.items.push(@json($element->jsonValues()));
                } else {
                    this.items = [@json($element->jsonValues())];
                }

                this.$nextTick(() => {
                    let newRow = this.$root.querySelector('[data-id=""]:last-child');
                    let removeable = newRow.querySelectorAll('.x-removeable');

                    if(removeable !== null) {
                        for (let i = 0; i < removeable.length; i++) {
                            removeable[i].remove();
                        }
                    }
                });

            },
            removeField(index) {
                this.items.splice(index, 1);
            },
        }
    }
</script>
