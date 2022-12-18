<div>
    @if($field->formViewValue($item))
        @if($field->isMultiple())
            <div>
                @if($field->attributes()->has('x-model'))
                    <div x-data="{imageValue: {{ $field->attributes()->get('x-model', '') }}}" class="grid md:grid-cols-2 sm:grid-cols-1 lg:grid-cols-3 m-5 mb-10">
                        <template x-for="(image, index) in imageValue" :key="index">
                            <div :id="'hidden_parent_{{ $field->id() }}' + index" class="relative bg-white p-3 m-2 border-1 border-dashed border-gray-100 shadow-md rounded-lg overflow-hidden">
                                <input x-ref="hidden_{{ $field->id() }}"
                                       type="hidden"
                                       :value="image"
                                       :name="'hidden_' + {{ $field->attributes()->get('x-bind:name') }} + '[]'"
                                />

                                <img class="w-full"
                                     :src="image ? ('{{ $field->path('') }}') + image : ''"
                                />

                                @if($field->isRemovable())
                                    <div class="p-4 absolute top-0 right-0">
                                        <div class="text-center">
                                            <a href="#" @click="$event.target.closest('#hidden_parent_{{ $field->id() }}' + index).remove()"
                                               class="px-4 py-2 bg-red-500 shadow-lg border rounded-lg text-white uppercase font-semibold tracking-wider focus:outline-none focus:shadow-outline hover:bg-red-400 active:bg-red-400">
                                                {{ trans('moonshine::ui.delete') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </template>
                    </div>
                @else
                    <div class="grid md:grid-cols-2 sm:grid-cols-1 lg:grid-cols-3 m-5 mb-10">
                        @foreach($field->formViewValue($item) as $index => $file)
                            <div x-data="{}"
                                 x-ref="hidden_parent_{{ $field->id() }}"
                                 class="relative bg-white p-3 m-2 border-1 border-dashed border-gray-100 shadow-md rounded-lg overflow-hidden"
                            >

                                <input x-ref="hidden_{{ $field->id() }}"
                                       type="hidden"
                                       value="{{ $file }}"
                                       name="hidden_{{ $field->name() }}"
                                />

                                <img class="w-full"
                                     @click.stop="$dispatch('img-modal', {imgModal: true, imgModalSrc: '{{ $field->path($file) }}' })"
                                     src="{{ $field->path($file) }}"
                                />

                                @if($field->isRemovable())
                                    <div class="p-4 absolute top-0 right-0">
                                        <div class="text-center">
                                            <a href="#" @click="$refs.hidden_parent_{{ $field->id() }}.remove();"
                                               class="px-4 py-2 bg-red-500 shadow-lg border rounded-lg text-white uppercase font-semibold tracking-wider focus:outline-none focus:shadow-outline hover:bg-red-400 active:bg-red-400">
                                                {{ trans('moonshine::ui.delete') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div
                @if($field->attributes()->has('x-model'))
                    x-data="{imageValue: {{ $field->attributes()->get('x-model', '') }}}"
                @else
                    x-data="{imageValue: ''}"
                @endif

                class="max-w-sm rounded overflow-hidden shadow-lg my-2"
            >
                <img class="w-full"
                     @if($field->attributes()->has('x-model'))
                         :src="imageValue ? ('{{ $field->path('') }}') + imageValue : ''"
                     @else
                         @click.stop="$dispatch('img-modal', {imgModal: true, imgModalSrc: '{{ $field->path($field->formViewValue($item)) }}' })"
                     src="{{ $field->path($field->formViewValue($item)) }}"
                    @endif
                />
            </div>
        @endif
    @endif

    @include("moonshine::fields.input", [
        "field" => $field,
        "item" => $item
    ])
</div>
