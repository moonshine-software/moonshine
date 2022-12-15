<div>
    @if($field->formViewValue($item))
        @if($field->isMultiple())
            <div class="my-2">
                @if($field->attributes()->has('x-model'))
                    <div x-data="{fileValue: {{ $field->attributes()->get('x-model', '') }}}" class="border border-gray-200 rounded-md">
                        <template x-for="(file, index) in fileValue" :key="index">
                            <div :id="'hidden_parent_{{ $field->id() }}' + index" class="pl-3 pr-4 py-3 flex items-center justify-between text-sm leading-5">
                                <input x-ref="hidden_{{ $field->id() }}"
                                       type="hidden"
                                       :value="file"
                                       :name="'hidden_' + {{ $field->attributes()->get('x-bind:name') }} + '[]'"
                                />

                                <div class="my-2">
                                    <div class="flex-1 flex items-center">
                                        @include('moonshine::shared.icons.clip', [
                                            'class' => 'flex-shrink-0'
                                        ])

                                        <span class="ml-2 flex-1 truncate"
                                              x-text="'@lang('moonshine::ui.file') ' + (index + 1)"
                                        ></span>

                                        @if($field->canDownload())
                                            <div class="ml-4 flex-shrink-0">
                                                <a :href="file ? ('{{ Storage::url('') }}') + file : ''"
                                                   download
                                                   class="font-medium text-pink hover:text-pink transition duration-150 ease-in-out">
                                                    {{ trans('moonshine::ui.download') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($field->isRemovable())
                                    <div class="ml-4 flex-shrink-0">
                                        <a href="#" @click="$event.target.closest('#hidden_parent_{{ $field->id() }}' + index).remove()"
                                           class="font-medium text-pink hover:text-pink transition duration-150 ease-in-out">
                                            {{ trans('moonshine::ui.delete') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </template>
                    </div>
                @else
                    <div class="border border-gray-200 rounded-md">
                        @foreach($field->formViewValue($item) as $index => $file)
                            <div x-data="{}"
                                 x-ref="hidden_parent_{{ $field->id() }}"
                                 class="pl-3 pr-4 py-3 flex items-center justify-between text-sm leading-5"
                            >

                                <input x-ref="hidden_{{ $field->id() }}"
                                       type="hidden"
                                       value="{{ $file }}"
                                       name="hidden_{{ $field->name() }}"
                                />

                                @include('moonshine::fields.shared.file', [
                                    'index' => $index+1,
                                    'canDownload' => $field->canDownload(),
                                    'value' => $file,
                                    'field' => $field,
                                ])

                                @if($field->isRemovable())
                                    <div class="ml-4 flex-shrink-0">
                                        <a href="#" @click="$refs.hidden_parent_{{ $field->id() }}.remove();"
                                           class="font-medium text-pink hover:text-pink transition duration-150 ease-in-out">
                                            {{ trans('moonshine::ui.delete') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            @include('moonshine::fields.shared.file', [
                'canDownload' => $field->canDownload(),
                'value' => $field->formViewValue($item),
                'field' => $field,
            ])
        @endif
    @endif

    @include("moonshine::fields.input", [
        "field" => $field,
        "item" => $item
    ])
</div>
