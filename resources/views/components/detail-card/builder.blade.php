<x-moonshine::box
        title="{{ $title }}"
>
    <x-moonshine::table>
        <x-slot:tbody>
            @foreach($fields as $field)
                <tr>
                    <th width="15%">
                        {{$field->label()}}
                    </th>
                    <td>{!! $field->preview() !!}</td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-moonshine::table>

    @if($buttons->isNotEmpty())
        <x-moonshine::column colSpan="12" adaptiveColSpan="8">
            <div class="mt-3 flex w-full flex-wrap justify-end gap-2">
                <x-moonshine::action-group :actions="$buttons"/>
            </div>
        </x-moonshine::column>
    @endif
</x-moonshine::box>
