<div x-data="handler_{{ $field->id() }}()" x-init="handler_init_{{ $field->id() }}">
    <template x-for="(item, index{{ $level }}) in items" :key="index{{ $level }}">
        <div :data-id="item.id" class="full_fields_{{ $field->id() }}">
            @if(!method_exists($field, 'isRelationToOne') || !$field->isRelationToOne())
                <div class="font-bold text-purple my-4" x-text="index{{ $level }} + 1"></div>
            @endif

            @foreach($field->getFields() as $subField)
                <x-moonshine::field-container :field="$subField" :item="$model" :resource="$resource">
                    {{ $resource->renderField($subField, $model, $level+1) }}
                </x-moonshine::field-container>
            @endforeach

            @if(!method_exists($field, 'isRelationToOne') || !$field->isRelationToOne())
                <div class="my-4">
                    @if($field->isRemovable())
                        <button @click="removeField(index{{ $level }})" type="button" class="text-pink hover:text-pink inline-block">
                            @include("moonshine::shared.icons.delete", ["size" => 6, "color" => "pink", "class" => "mr-2"])
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </template>

    <div class="my-4">
    @if(!method_exists($field, 'isRelationToOne') || !$field->isRelationToOne())
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
