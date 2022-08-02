<div x-data="handler_{{ $element->id() }}()" x-init="handler_init_{{ $element->id() }}">
    <template x-for="(item, index{{ $level ?? 0 }}) in items" :key="index{{ $level ?? 0 }}">
        <div :data-id="item.id" class="full_fields_{{ $element->id() }}">
            @if(!$element->toOne())
                <div class="font-bold text-purple my-4" x-text="index{{ $level ?? 0 }} + 1"></div>
            @endif

            @foreach($element->getFields() as $child)
                <x-moonshine::field-container :field="$child">
                    {!! $child !!}
                </x-moonshine::field-container>
            @endforeach

            @if(!$element->toOne())
                <div class="my-4">
                    @if($element->isRemovable())
                        <button @click="removeField(index{{ $level ?? 0 }})" type="button"
                                class="text-pink hover:text-pink inline-block">
                            @include("moonshine::shared.icons.delete", ["size" => 6, "color" => "pink", "class" => "mr-2"])
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </template>

    <div class="my-4">
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
    </div>
</div>

<script>
    function handler_{{ $element->id() }}() {
        return {
            handler_init_{{ $element->id() }} () {
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
