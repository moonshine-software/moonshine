@props([
    'errors' => [],
    'name' => '',
    'label' => '',
    'beforeLabel' => false,
    'inLabel' => false,
    'beforeSlot',
    'afterSlot',
    'formName' => '',
])
<div {{ $attributes->merge(['class' => 'form-group moonshine-field'])->except('required') }}
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

    <div>
        {{ $beforeSlot ?? '' }}

        {{ !$beforeLabel && !$inLabel ? $slot : '' }}

        {{ $afterSlot ?? '' }}
    </div>

    @if(isset($errors[0]))
        <x-moonshine::form.input-error>
            {{ $errors[0] }}
        </x-moonshine::form.input-error>
    @endif
</div>
