<div class="my-2">
    <div class="flex-1 flex items-center">
        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" x-description="Heroicon name: paper-clip" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
        </svg>

        <span class="ml-2 flex-1 truncate">
            Файл {{ $index ?? '' }}
        </span>

        @if($canDownload)
            <div class="ml-4 flex-shrink-0">
                <a href="{{ Storage::url($value) }}" download class="font-medium text-pink hover:text-pink transition duration-150 ease-in-out">
                    {{ trans('moonshine::ui.download') }}
                </a>
            </div>
        @endif
    </div>
</div>