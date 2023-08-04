<div>
    <x-moonshine::table
            :classic="!$preview"
            :notfound="$notfound"
            :attributes="$attributes"
    >
        @if(!$vertical)
            <x-slot:thead>
                <x-moonshine::table.head
                    :fields="$fields"
                    :actions="$bulkButtons"
                />
            </x-slot:thead>
        @endif

        @if($rows->isNotEmpty())
            <x-slot:tbody>
                <x-moonshine::table.body
                    :rows="$rows"
                    :vertical="$vertical"
                    :editable="$editable"
                    :actions="$bulkButtons"
                />
            </x-slot:tbody>
        @endif

        @if($creatable)
            <x-slot:tfoot>
                <td colspan="{{ $fields->count() + 2 }}">
                    <x-moonshine::link
                        class="w-full"
                        icon="heroicons.plus-circle"
                        :x-show="$toOne ?? false ? 'items.length == 0' : 'true'"
                        @click.prevent="add()"
                    >
                        @lang('moonshine::ui.' . ($toOne ?? false ? 'create' : 'add'))
                    </x-moonshine::link>
                </td>
            </x-slot:tfoot>
        @else
            <x-slot:tfoot
                x-ref="foot"
                ::class="actionsOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'"
            >
                <x-moonshine::table.foot
                    :rows="$rows"
                    :actions="$bulkButtons"
                />
            </x-slot:tfoot>
        @endif
    </x-moonshine::table>

    @if($hasPaginator)
        {{ $paginator->links('moonshine::ui.pagination') }}
    @endif
</div>
