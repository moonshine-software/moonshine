@props([
    'value' => null,
    'values' => null,
    'alt' => ''
])
@if($value)
    <div class="flex">
        <div class="zoom-in h-10 w-10 overflow-hidden rounded-md">
            <img {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}
                 src="{{ $value }}"
                 alt="{{ $alt }}"
            >
        </div>
    </div>
@elseif($values)
    <div class="images-row">
        @foreach($values as $value)
            <div class="zoom-in images-row-item">
                <img {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}
                     src="{{ $value }}"
                     alt="{{ $alt }}"
                />
            </div>
        @endforeach
    </div>
@endif

