<a href="{{ $href }}"
   class="inline-flex  items-center
   @if(!$filled)
       bg-transparent hover:bg-purple text-purple border border-purple hover:text-white hover:border-transparent
   @else
       bg-gradient-to-r from-purple to-pink text-white
    @endif
   font-semibold  py-2 px-4 rounded"
>
    @includeWhen($icon !== false, "moonshine::shared.icons.$icon", [
        "size" => 4,
        "class" => "mr-2",
    ])

    <span>{{ $title }}</span>
</a>