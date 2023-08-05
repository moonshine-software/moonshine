@php
    $level = 0;
@endphp

<x-moonshine::table
    x-data="tableFields({{
    $element->attributes()->get('x-model-has-fields')
            ? 'item.'.$element->column()
            : json_encode($element->jsonValues())
    }})"
    data-empty="{{ json_encode($element->jsonValues()) }}"
    data-input-table="{{ str_replace('[]', '', $element->name()) }}"
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
        @foreach($element->value()->rows() as $level => $row)
            <tr :data-id="key(item, index{{ $level }})" class="table_fields_{{ $element->id() }}">
                @if(!$element->isVertical())
                    <td class="text-center" scope="row" x-text="index{{ $level }} + 1"></td>

                    @foreach($row->getFields() as $subField)
                        <td class="space-y-3">
                            {{ $subField->render() }}
                        </td>
                    @endforeach

                    @if($element->isRemovable())
                        <td>
                            <button type="button" @click.prevent="remove(index{{ $level }})" class="badge badge-red">&times;</button>
                        </td>
                    @endif
                @else
                    <th width="5%" class="text-center" x-text="index{{ $level }} + 1"></th>

                    <td class="space-y-3">
                        @foreach($row->getFields() as $subField)
                            <x-moonshine::field-container :field="$subField">
                                {{ $subField->render() }}
                            </x-moonshine::field-container>
                        @endforeach
                    </td>

                    @if($element->isRemovable())
                        <td width="5%" class="text-center">
                            <button type="button" @click.prevent="remove(index{{ $level }})" class="badge badge-red">&times;</button>
                        </td>
                    @endif
                @endif
            </tr>
        @endforeach

        <template
            x-for="(item, index{{ $level }}) in items"
            :key="key(item, index{{ $level }})"
        >
            <tr :data-id="key(item, index{{ $level }})" class="table_fields_{{ $element->id() }}">
                @if(!$element->isVertical())
                    <td class="text-center" scope="row" x-text="index{{ $level }} + 1"></td>

                    @foreach($element->getFields() as $subField)
                        <td class="space-y-3">
                            {{ $subField->render() }}
                        </td>
                    @endforeach

                    @if($element->isRemovable())
                        <td>
                            <button type="button" @click.prevent="remove(index{{ $level }})" class="badge badge-red">&times;</button>
                        </td>
                    @endif
                @else
                    <th width="5%" class="text-center" x-text="index{{ $level }} + 1"></th>

                    <td class="space-y-3">
                        @foreach($element->getFields() as $subField)
                            <x-moonshine::field-container :field="$subField">
                                {{ $subField->render() }}
                            </x-moonshine::field-container>
                        @endforeach
                    </td>

                    @if($element->isRemovable())
                        <td width="5%" class="text-center">
                            <button type="button" @click.prevent="remove(index{{ $level }})" class="badge badge-red">&times;</button>
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
