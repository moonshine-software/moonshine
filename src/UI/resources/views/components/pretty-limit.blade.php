@props([
    'color' => 'secondary',
    'label' => null,
    'limit' => 150,
])
<span {{ $attributes->merge(['class' => 'pretty-limit']) }}
      @if($limit) style="--pretty-limit-width: {{ $limit }}px" @endif
>
    <span class="pretty-limit-inner {{ $color ? 'pretty-limit-'.$color : '' }}">
        @if($label)
            <span class="pretty-limit-label">{{ $label }}</span>
        @endif
        <span class="pretty-limit-value">{{ $slot }}</span>
    </span>
</span>
