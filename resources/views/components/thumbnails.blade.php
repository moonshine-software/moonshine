@props([
    'value' => null,
    'values' => null,
    'alt' => '',
    'names' => null,
    'itemAttributes' => null,
])
@if($value)
    <div class="flex">
        <div
            {{ value($itemAttributes, $value)->merge(['class' => 'zoom-in h-10 w-10 overflow-hidden rounded-md']) }}
        >
            <img class="h-full w-full object-cover"
                 src="{{ $value }}"
                 alt="{{ value($names, $value) ?? $alt }}"
                 @click.stop="$dispatch('img-popup', {open: true, src: '{{ $value }}' })"
            >
        </div>
    </div>
@elseif($values)
    <div class="images-row">
        @foreach($values as $index => $value)
            <div
                {{ value($itemAttributes, $value, $index)->merge(['class' => 'zoom-in images-row-item']) }}
            >
                <img
                    class="h-full w-full object-cover"
                     src="{{ $value }}"
                     alt="{{ value($names, $value, $index) ?? $alt }}"
                     @click.stop="$dispatch('img-popup', {open: true, src: '{{ $value }}' })"
                />
            </div>
        @endforeach
    </div>
@endif

