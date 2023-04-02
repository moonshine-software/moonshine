@props([
    'label',
    'value' => '',
    'withFields' => false
])
<x-moonshine::form.input-wrapper
    :attributes="$attributes->only(['id', 'name', 'x-bind:id'])"
    class="form-group-inline !m-0"
    :label="$label"
    :beforeLabel="true"
    :inLabel="false"
>
    <x-moonshine::form.input
        :attributes="$attributes->merge([
            'class' => 'pivotChecker',
            'type' => 'checkbox',
            'value' => $value,
        ])"
    />
</x-moonshine::form.input-wrapper>

@if($withFields)
    <div
        id="wrapper_{{ $attributes->get('id') }}_pivots"
        class="pivotFields mt-1"
        {!! $attributes->get('x-bind:id') ? 'x-bind:id="`wrapper_'.str_replace('`', '', $attributes->get('x-bind:id')).'_pivots`"' : '' !!}
    >
        <x-moonshine::form.input-wrapper
            class="form-group-inline w-full"
        >
            {{ $slot }}
        </x-moonshine::form.input-wrapper>
    </div>
@endif
