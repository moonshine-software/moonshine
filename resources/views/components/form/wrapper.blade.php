@props([
    'label' => '',
    'error' => null,
    'beforeLabel' => false,
    'insideLabel' => false,
    'before',
    'after',
])
<div {{ $attributes->merge(['class' => 'form-group moonshine-field'])->except('required') }}
     x-id="['input-wrapper', 'field']" :id="$id('input-wrapper')"
>
    {{ $beforeLabel && !$insideLabel ? $slot : '' }}

    @if($label)
        <x-moonshine::form.label
            :required="$attributes->get('required', false)"
            ::for="$id('field')"
        >
            {{ $beforeLabel && $insideLabel ? $slot : '' }}
            {!! $label !!}
            {{ !$beforeLabel && $insideLabel ? $slot : '' }}
        </x-moonshine::form.label>
    @endif

    <div>
        {{ $before ?? '' }}

        {{ !$beforeLabel && !$insideLabel ? $slot : '' }}

        {{ $after ?? '' }}
    </div>

    @if($error)
        <x-moonshine::form.input-error>
            {{ $error }}
        </x-moonshine::form.input-error>
    @endif
</div>
