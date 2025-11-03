@props([
    'value' => null,
    'values' => null,
    'alt' => '',
])
@if($value)
    <div class="flex">
        <div
            {{ $value['attributes']?->class(['zoom-in h-10 w-10 overflow-hidden rounded-md bg-white dark:bg-base-700 cursor-pointer']) }}
        >
            <img class="h-full w-full object-cover"
                 src="{{ $value['full_path'] }}"
                 alt="{{ $value['name'] ?? $alt }}"
                 @click.stop="$dispatch('img-popup', {
                    open: true,
                    src: '{{ $value['full_path']  }}',
                    wide: {{ isset($value['extra']['wide']) && $value['extra']['wide'] ? 'true' : 'false'  }},
                    auto: {{ isset($value['extra']['auto']) && $value['extra']['auto'] ? 'true' : 'false'  }},
                    styles: '{{ $value['extra']['content_styles'] ?? ''  }}'
                 })"
            >
        </div>
    </div>
@elseif($values !== [])
    <div class="images-row">
        @foreach($values as $index => $value)
            <div
                {{ $value['attributes']?->class(['zoom-in images-row-item bg-base cursor-pointer']) }}
            >
                <img
                    class="h-full w-full object-cover"
                    src="{{ $value['full_path'] }}"
                    alt="{{ $value['name'] ?? $alt }}"
                    @click.stop="$dispatch('img-popup', {
                        open: true,
                        src: '{{ $value['full_path']  }}',
                        wide: {{ isset($value['extra']['wide']) && $value['extra']['wide'] ? 'true' : 'false'  }},
                        auto: {{ isset($value['extra']['auto']) && $value['extra']['auto'] ? 'true' : 'false'  }},
                        styles: '{{ $value['extra']['content_styles'] ?? ''  }}'
                    })"
                />
            </div>
        @endforeach
    </div>
@endif
