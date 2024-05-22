@props([
    'value' => null,
    'values' => null,
    'alt' => '',
])
@if($value)
    <div class="flex">
        <div
            {{ $value['attributes']?->class(['zoom-in h-10 w-10 overflow-hidden rounded-md']) }}
        >
            <img class="h-full w-full object-cover"
                 src="{{ $value['full_path'] }}"
                 alt="{{ $value['name'] ?? $alt }}"
                 @click.stop="$dispatch('img-popup', {open: true, src: '{{ $value['full_path']  }}' })"
            >
        </div>
    </div>
@elseif($values !== [])
    <div class="images-row">
        @foreach($values as $index => $value)
            <div
                {{ $value['attributes']?->class(['zoom-in images-row-item']) }}
            >
                <img
                    class="h-full w-full object-cover"
                    src="{{ $value['full_path'] }}"
                    alt="{{ $value['name'] ?? $alt }}"
                    @click.stop="$dispatch('img-popup', {open: true, src: '{{ $value['full_path']  }}' })"
                />
            </div>
        @endforeach
    </div>
@endif
