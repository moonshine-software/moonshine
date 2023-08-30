@props([
    'value' => 1,
    'min' => 1,
    'max' => 5
])
<div {{ $attributes->merge(['class' => 'flex items-center gap-x-1']) }}>
    @for($star = $min; $star <= $max; $star++)
        <svg class="w-4 h-4 fill-current @if($star <= $value) text-secondary @else text-gray-400 @endif"
             xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 20 20"
        >
            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
        </svg>
    @endfor
</div>
