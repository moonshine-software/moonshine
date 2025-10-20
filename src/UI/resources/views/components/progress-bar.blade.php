@props([
    'radial' => false,
    'value' => 0,
    'color' => '',
    'size' => 'md'
])
@if($radial)
    <div {{ $attributes->class(['radial-progress', "radial-progress--$color" => $color, "radial-progress-$size" => $size]) }}
         style="--value: {{ $value }}"
    >
        <span class="radial-progress-label">{{ $slot ?? $value . '%' }}</span>
    </div>
@else
    <div {{ $attributes->class(['progress']) }}>
        <div class="progress-bar {{ $color ? "progress-bar--$color" : '' }}"
             role="progressbar"
             style="width: {{ $value }}%"
        >{{ $slot ?? $value . '%' }}</div>
    </div>
@endif
