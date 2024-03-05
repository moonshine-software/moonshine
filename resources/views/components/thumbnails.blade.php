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
            @if(is_null($itemAttributes))
                class="zoom-in h-10 w-10 overflow-hidden rounded-md"
            @else
                {{ value($itemAttributes, $value)?->class(['zoom-in h-10 w-10 overflow-hidden rounded-md']) }}
            @endif
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
                @if(is_null($itemAttributes))
                    class="zoom-in images-row-item"
                @else
                    {{ value($itemAttributes, $value, $index)?->class(['zoom-in images-row-item']) }}
                @endif
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
