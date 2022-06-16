<div class="flex justify-left items-center">
    <div class="flex items-center mt-2 mb-4">
        @for($star = 1; $star <= 5; $star++)
            <svg class="mx-1 w-4 h-4 fill-current @if($star <= $value) text-pink @else text-gray-400 @endif" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
        @endfor
    </div>
</div>