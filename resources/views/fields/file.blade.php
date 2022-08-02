<div>
    @if($element->value())
        @if($element->isMultiple())
            <div class="my-2">
                <div class="border border-gray-200 rounded-md">
                    @foreach($element->value() as $index => $file)
                        <div x-data="{}" x-ref="hidden_parent_{{ $element->id() }}"
                             class="pl-3 pr-4 py-3 flex items-center justify-between text-sm leading-5">
                            <input x-ref="hidden_{{ $element->id() }}" type="hidden"
                                   name="hidden_{{ $element->name() }}"
                                   value="{{ $file }}"/>

                            @include('moonshine::fields.shared.file', [
                                'index' => $index+1,
                                'canDownload' => $element->canDownload(),
                                'value' => $file
                            ])

                            @if($element->isRemovable())
                                <div class="ml-4 flex-shrink-0">
                                    <a href="#" @click="$refs.hidden_parent_{{ $element->id() }}.remove();"
                                       class="font-medium text-pink hover:text-pink transition duration-150 ease-in-out">
                                        {{ trans('moonshine::ui.delete') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            @include('moonshine::fields.shared.file', [
                'canDownload' => $element->canDownload(),
                'value' => $element->value()
            ])
        @endif
    @endif

    @include("moonshine::fields.input", [
        'element' => $element
    ])
</div>

