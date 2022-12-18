<div class="my-2"
     @if(isset($field) && $field->attributes()->has('x-model'))
         x-data="{fileValue: {{ $field->attributes()->get('x-model', '') }}}"
     @else
         x-data="{fileValue: ''}"
    @endif
>
    <div class="flex-1 flex items-center">
        @include('moonshine::shared.icons.clip', [
            'class' => 'flex-shrink-0'
        ])

        <span class="ml-2 flex-1 truncate">
            @lang('moonshine::ui.file') {{ $index ?? '' }}
        </span>

        @if($canDownload)
            <div class="ml-4 flex-shrink-0">
                <a @if(isset($field) && $field->attributes()->has('x-model'))
                       :href="fileValue ? ('{{ $field->path('') }}') + fileValue : ''"
                   @else
                       href="{{ $value }}"
                   @endif
                   download
                   class="font-medium text-pink hover:text-pink transition duration-150 ease-in-out">
                    {{ trans('moonshine::ui.download') }}
                </a>
            </div>
        @endif
    </div>
</div>
