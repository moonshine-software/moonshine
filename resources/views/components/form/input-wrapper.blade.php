@props([
    'name' => '',
    'label' => '',
    'beforeLabel' => false,
    'expansion' => null
])
<div {{ $attributes->class(['form-group-expansion' => $expansion])
    ->merge(['class' => 'form-group'])
    ->only(['class', 'x-show']) }}
>
    {{ $beforeLabel ? $slot : '' }}

    @if($label)
        <x-moonshine::form.label
            for="{{ $name }}"
            :attributes="$attributes->only('required')"
        >
            {{ $label }}
        </x-moonshine::form.label>
    @endif

    {{ !$beforeLabel ? $slot : '' }}

    @if($expansion)
        <span class="expansion">{{ $expansion }}</span>
    @endif

    @error($name)
    <x-moonshine::form.input-error>
        {{ $message }}
    </x-moonshine::form.input-error>
    @enderror
</div>
