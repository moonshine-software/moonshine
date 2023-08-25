@props([
    'value' => null,
    'values' => null,
    'alt' => ''
])
@if($value)
    <div class="flex">
        <div class="zoom-in h-10 w-10 overflow-hidden rounded-md">
            <img {{ $attributes->merge(['class' => 'h-full w-full object-cover cursor-zoom-in']) }}
                 src="{{ $value }}"
                 alt="{{ $alt }}"
                 @click.stop="$dispatch('img-popup', {open: true, src: '{{ $value }}' })"
            >
        </div>
    </div>
@elseif($values)
    <div class="images-row">
        @foreach($values as $value)
            <div class="zoom-in images-row-item">
                <img {{ $attributes->merge(['class' => 'h-full w-full object-cover cursor-zoom-in']) }}
                     src="{{ $value }}"
                     alt="{{ $alt }}"
                     @click.stop="$dispatch('img-popup', {open: true, src: '{{ $value }}' })"
                />
            </div>
        @endforeach
    </div>
@endif

