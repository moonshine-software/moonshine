<div>
    @if($field->formViewValue($item))
        @if($field->isMultiple())
            <div class="my-2">
                <div class="border border-gray-200 rounded-md">
                    @foreach($field->formViewValue($item) as $index => $file)
                        <div x-data="{}" x-ref="hidden_parent_{{ $field->id() }}"  class="pl-3 pr-4 py-3 flex items-center justify-between text-sm leading-5">
                            <input x-ref="hidden_{{ $field->id() }}" type="hidden" name="hidden_{{ $field->name() }}" value="{{ $file }}" />

                            @include('moonshine::fields.shared.file', [
                                'index' => $index+1,
                                'canDownload' => $field->canDownload(),
                                'value' => $file
                            ])

                            @if($field->isRemovable())
                                <div class="ml-4 flex-shrink-0">
                                    <a href="#" @click="$refs.hidden_parent_{{ $field->id() }}.remove();" class="font-medium text-pink hover:text-pink transition duration-150 ease-in-out">
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
                'canDownload' => $field->canDownload(),
                'value' => $field->formViewValue($item)
            ])
        @endif
    @endif

    @include("moonshine::fields.input", [
        'field' => $field,
        'item' => $item
    ])
</div>

