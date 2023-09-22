@props([
    'name' => '',
    'label' => '',
    'beforeLabel' => false,
    'inLabel' => false,
    'beforeSlot',
    'afterSlot',
    'formName' => ''
])
<div {{ $attributes->merge(['class' => 'form-group moonshine-field'])
    ->only(['class', 'x-show']) }}
     x-id="['input-wrapper']" :id="$id('input-wrapper')"
>
    {{ $beforeLabel && !$inLabel ? $slot : '' }}

    @if($label)
        <x-moonshine::form.label
            for="{{ $attributes->get('id', $name) }}"
            :attributes="$attributes->only('required')"
        >
            {{ $beforeLabel && $inLabel ? $slot : '' }}
            {!! $label !!}
            {{ !$beforeLabel && $inLabel ? $slot : '' }}
        </x-moonshine::form.label>
    @endif

    {{ $beforeSlot ?? '' }}

    {{ !$beforeLabel && !$inLabel ? $slot : '' }}

    {{ $afterSlot ?? '' }}

    @error($name, $formName)
        <x-moonshine::form.input-error>
            {{ $message }}
        </x-moonshine::form.input-error>
    @enderror
</div>
