<div class="flex items-center">
    @if($image)
        <img src="{{ $image }}" class="w-8 h-8 object-cover rounded-full" alt="{{ $value }}">
    @endif

    @if($link)
        <a class="text-gray-700 text-sm mx-3" href="{{ $link }}">{{ $value }}</a>
    @else
        <span class="text-gray-700 text-sm mx-3">{{ $value }}</span>
    @endif
</div>