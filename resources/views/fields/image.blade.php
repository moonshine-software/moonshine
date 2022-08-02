<div>
    @if($element->value())
        @if($element->isMultiple())
            <div>
                <div class="grid md:grid-cols-2 sm:grid-cols-1 lg:grid-cols-3 m-5 mb-10">
                    @foreach($element->value() as $index => $file)
                        <div x-data="{}" x-ref="hidden_parent_{{ $element->id() }}"
                             class="relative bg-white p-3 m-2 border-1 border-dashed border-gray-100 shadow-md rounded-lg overflow-hidden">
                            <input x-ref="hidden_{{ $element->id() }}" type="hidden"
                                   name="hidden_{{ $element->name() }}"
                                   value="{{ $file }}"/>

                            <img @click.stop="$dispatch('img-modal', {imgModalSrc: '{{ Storage::url($file) }}' })"
                                 class="w-full object-cover object-center rounded"
                                 src="{{ Storage::url($file) }}"
                            />

                            @if($element->isRemovable())
                                <div class="p-4 absolute top-0 right-0">
                                    <div class="text-center">
                                        <a href="#" @click="$refs.hidden_parent_{{ $element->id() }}.remove();"
                                           class="px-4 py-2 bg-red-500 shadow-lg border rounded-lg text-white uppercase font-semibold tracking-wider focus:outline-none focus:shadow-outline hover:bg-red-400 active:bg-red-400">
                                            {{ trans('moonshine::ui.delete') }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div x-data="{}" class="max-w-sm rounded overflow-hidden shadow-lg my-2">
                <img
                    @click.stop="$dispatch('img-modal', {imgModal: true, imgModalSrc: '{{ Storage::url($element->value()) }}' })"
                    class="w-full"
                    src="{{ Storage::url($element->value()) }}"
                />
            </div>
        @endif
    @endif

    @include("moonshine::fields.input", [
        'element' => $element,
    ])
</div>
