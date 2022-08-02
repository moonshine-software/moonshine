<div class="flex flex-col mt-8">
    <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg">
            <div x-data="handler_{{ $element->id() }}()" x-init="handler_init_{{ $element->id() }}">
                <table class="min-w-full">
                    <thead class="bg-whiteblue dark:bg-purple">
                    <tr>
                        @if(!$element->toOne())
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">#
                            </th>
                        @endif

                        @foreach($element->getFields() as $child)
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">
                                {{ $child->label() }}
                            </th>
                        @endforeach

                        @if(!$element->toOne())
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider"></th>
                        @endif
                    </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-darkblue text-black dark:text-white">
                    <template x-for="(item, index{{ $level ?? 0 }}) in items" :key="index{{ $level ?? 0 }}"
                    >
                        <tr :data-id="item.id" class="table_fields_{{ $element->id() }}">
                            @if(!$element->toOne())
                                <td class="px-6 py-4 whitespace-no-wrap" x-text="index{{ $level ?? 0 }} + 1"></td>
                            @endif

                            @foreach($element->getFields() as $child)
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    {!! $child !!}
                                </td>
                            @endforeach

                            @if(!$element->toOne())
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    @if($element->isRemovable())
                                        <button @click="removeField(index{{ $level ?? 0 }})" type="button"
                                                class="text-pink hover:text-pink inline-block">
                                            @include("moonshine::shared.icons.delete", ["size" => 6, "color" => "pink", "class" => "mr-2"])
                                        </button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    </template>
                    </tbody>

                    <tfoot class="bg-whiteblue dark:bg-purple">
                    <tr>
                        <td colspan="{{ count($element->getFields())+2 }}" class="px-6 py-4 whitespace-no-wrap">
                            @if(!$element->toOne())
                                <button type="button"
                                        class="bg-gradient-to-r from-purple to-pink text-white
    text-white font-semibold py-2 px-4 rounded"
                                        @click="addNewField()"
                                >
                                    @lang('moonshine::ui.add')
                                </button>
                            @else
                                <button x-show="items.length == 0" type="button"
                                        class="bg-gradient-to-r from-purple to-pink text-white
    text-white font-semibold py-2 px-4 rounded"
                                        @click="addNewField()"
                                >
                                    @lang('moonshine::ui.create')
                                </button>
                            @endif
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function handler_{{ $element->id() }}() {
        return {
            handler_init_{{ $element->id() }}() {
                this.items = @json($element);
            },
            items: [],
            addNewField() {
                if (Array.isArray(this.items)) {
                    this.items.push(@json($element));
                } else {
                    this.items = [@json($element)];
                }
            },
            removeField(index) {
                this.items.splice(index, 1);
            },
        }
    }
</script>
