<div class="flex flex-col mt-8">
    <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
            <div x-data="handler_{{ $field->id() }}()" x-init="handler_init_{{ $field->id() }}">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            @if(!method_exists($field, 'isRelationToOne') || !$field->isRelationToOne())
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">#</th>
                            @endif

                            @foreach($field->getFields() as $subField)
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $subField->label() }}
                                </th>
                            @endforeach

                            @if(!method_exists($field, 'isRelationToOne') || !$field->isRelationToOne())
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"></th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        <template x-for="(item, index) in items" :key="index"
                        >
                            <tr :data-id="item.id" class="table_fields_{{ $field->id() }}">
                                @if(!method_exists($field, 'isRelationToOne') || !$field->isRelationToOne())
                                    <td class="px-6 py-4 whitespace-no-wrap" x-text="index + 1"></td>
                                @endif

                                @foreach($field->getFields() as $subField)
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        {{ $resource->renderField($subField, $model) }}
                                    </td>
                                @endforeach

                                @if(!method_exists($field, 'isRelationToOne') || !$field->isRelationToOne())
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        @if($field->isRemovable())
                                            <button @click="removeField(index)" type="button" class="text-pink hover:text-pink inline-block">
                                                @include("moonshine::shared.icons.delete", ["size" => 6, "color" => "pink", "class" => "mr-2"])
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        </template>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="{{ count($field->getFields())+2 }}" class="px-6 py-4 whitespace-no-wrap">
                                @if(!method_exists($field, 'isRelationToOne') || !$field->isRelationToOne())
                                    <button type="button"
                                            class="bg-transparent hover:bg-purple text-purple font-semibold hover:text-white py-2 px-4 border border-purple hover:border-transparent rounded"
                                            @click="addNewField()"
                                    >
                                        Добавить
                                    </button>
                                @else
                                    <button x-show="items.length == 0" type="button"
                                            class="bg-transparent hover:bg-purple text-purple font-semibold hover:text-white py-2 px-4 border border-purple hover:border-transparent rounded"
                                            @click="addNewField()"
                                    >
                                        Создать
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
      handler_init_{{ $field->id() }} () {
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