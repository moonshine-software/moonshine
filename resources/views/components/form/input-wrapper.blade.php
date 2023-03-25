@props([
    'name' => '',
    'label' => '',
    'beforeLabel' => false,
    'inLabel' => false,
    'expansion' => null,
    'beforeSlot',
    'afterSlot'
])
<div {{ $attributes->merge(['class' => 'form-group'])
    ->only(['class', 'x-show']) }}
>
    {{ $beforeLabel && !$inLabel ? $slot : '' }}

    @if($label)
        <x-moonshine::form.label
            for="{{ $attributes->get('id', $name) }}"
            :attributes="$attributes->only('required')"
        >
            {{ $beforeLabel && $inLabel ? $slot : '' }}
            {{ $label }}
            {{ !$beforeLabel && $inLabel ? $slot : '' }}
        </x-moonshine::form.label>
    @endif

    {{ $beforeSlot ?? '' }}

    @if($expansion) <div class="form-group form-group-expansion"> @endif

    {{ !$beforeLabel && !$inLabel ? $slot : '' }}

    @if($expansion)
        <span class="expansion">{{ $expansion }}</span>
    @endif

    @if($expansion) </div> @endif

    {{ $afterSlot ?? '' }}

    @error($name)
    <x-moonshine::form.input-error>
        {{ $message }}
    </x-moonshine::form.input-error>
    @enderror
</div>
