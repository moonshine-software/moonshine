@props([
    'name' => '',
    'label' => '',
    'beforeLabel' => false,
    'inLabel' => false,
    'beforeSlot',
    'afterSlot'
])
<div {{ $attributes->merge(['class' => 'form-group'])
    ->only(['class', 'x-show']) }}
    id="wrapper_{{ $attributes->get('id') }}"
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

    @error($name)
        <x-moonshine::form.input-error>
            {{ $message }}
        </x-moonshine::form.input-error>
    @enderror
</div>
