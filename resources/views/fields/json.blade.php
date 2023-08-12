<x-moonshine::table
    x-data="tableFields({{
    $element->attributes()->get('x-model-has-fields')
            ? 'item.'.$element->column()
            : json_encode($element->values())
    }})"
    data-empty="{{ json_encode($element->values(empty: true)) }}"
    data-input-table="{{ str_replace('[]', '', $element->name()) }}"
    x-id="['{{ $element->id('table-field') }}']"
    ::id="$id('{{ $element->id('table-field') }}')"
>
    <x-slot:thead>
        @if(!$element->isVertical())
            <th width="5%" class="text-center">#</th>

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
            x-for="(item, index{{ $element->level() }}) in items"
            :key="key(item, index{{ $element->level() }})"
        >
            <tr :data-id="key(item, index{{ $element->level() }})" class="table_fields_{{ $element->id() }}">
                @if(!$element->isVertical())
                    <td class="text-center" x-text="index{{ $element->level() }} + 1"></td>

                    @foreach($element->getFields() as $subField)
                        <td class="space-y-3">
                            {{ $subField->render() }}
                        </td>
                    @endforeach

                    @if($element->isRemovable())
                        <td>
                            <button type="button" @click.prevent="remove(index{{ $element->level() }})" class="badge badge-red">&times;</button>
                        </td>
                    @endif
                @else
                    <td width="5%" class="text-center" x-text="index{{ $element->level() }} + 1"></td>

                    <td class="space-y-3">
                        @foreach($element->getFields() as $subField)
                            <x-moonshine::field-container :field="$subField">
                                {{ $subField->render() }}
                            </x-moonshine::field-container>
                        @endforeach
                    </td>

                    @if($element->isRemovable())
                        <td width="5%" class="text-center">
                            <button type="button" @click.prevent="remove(index{{ $element->level() }})" class="badge badge-red">&times;</button>
                        </td>
                    @endif
                @endif
            </tr>
        </template>
    </x-slot:tbody>

    <x-slot:tfoot>
        <td colspan="{{ $element->getFields()->count() + 2 }}">
            <x-moonshine::link
                class="w-full"
                icon="heroicons.plus-circle"
                @click.prevent="add()"
            >
                @lang('moonshine::ui.add')
            </x-moonshine::link>
        </td>
    </x-slot:tfoot>
</x-moonshine::table>
