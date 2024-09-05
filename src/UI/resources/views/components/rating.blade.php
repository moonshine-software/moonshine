@props([
    'value' => 1,
    'min' => 1,
    'max' => 5
])
<div {{ $attributes->merge(['class' => 'flex items-center gap-x-1']) }}>
    @for($star = $min; $star <= $max; $star++)
        <x-moonshine::icon icon="star" size="4" :class="($star <= $value) ? 'text-secondary' : 'text-gray-400'"/>
    @endfor
</div>
