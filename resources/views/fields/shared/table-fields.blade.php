<div class="flex flex-col mt-8">
    <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg">
            <div x-data="handler_{{ $field->id() }}()" x-init="handler_init_{{ $field->id() }}">
                <table class="min-w-full">
                    <thead class="bg-whiteblue dark:bg-purple">
                    <tr>
                        @if(!$field->toOne())
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">#</th>
                        @endif

                        @foreach($field->getFields() as $subField)
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">
                                {{ $subField->label() }}
                            </th>
                        @endforeach

                        @if(!$field->toOne())
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider"></th>
                        @endif
                    </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-darkblue text-black dark:text-white">
                    <template x-for="(item, index{{ $level }}) in items" :key="index{{ $level }}"
                    >
                        <tr :data-id="item.id" class="table_fields_{{ $field->id() }}">
                            @if(!$field->toOne())
                                <td class="px-6 py-4 whitespace-nowrap" x-text="index{{ $level }} + 1"></td>
                            @endif

                            @foreach($field->getFields() as $subField)
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $resource->renderField($subField, $model, $level+1) }}
                                </td>
                            @endforeach

                            @if(!$field->toOne())
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($field->isRemovable())
                                        <button @click="removeField(index{{ $level }})" type="button" class="text-pink hover:text-pink inline-block">
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
                        <td colspan="{{ count($field->getFields())+2 }}" class="px-6 py-4 whitespace-nowrap">
                            @if(!$field->toOne())
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
