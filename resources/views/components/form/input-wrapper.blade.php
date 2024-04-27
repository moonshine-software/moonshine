@props([
    'name' => '',
    'label' => '',
    'beforeLabel' => false,
    'inLabel' => false,
    'beforeSlot',
    'afterSlot',
    'formName' => '',
])
<div {{ $attributes->merge(['class' => 'form-group moonshine-field'])
    ->only(['class', 'x-show', 'style']) }}
     x-id="['input-wrapper', 'field']" :id="$id('input-wrapper')"
>
    {{ $beforeLabel && !$inLabel ? $slot : '' }}

    @if($label)
        <x-moonshine::form.label
            :attributes="$attributes->only(['required'])"
            ::for="$id('field')"
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
