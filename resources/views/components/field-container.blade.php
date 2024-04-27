@props([
    'label' => '',
    'name' => '',
    'formName' => '',
    'labelBefore' => false,
    'inLabel' => false,
    'before' => null,
    'after' => null,
    'beforeInner' => null,
    'afterInner' => null,
])
{!! $before !!}

<x-moonshine::form.input-wrapper
    label="{{ $label }}"
    name="{{ $name }}"
    :attributes="$attributes"
    :formName="$formName"
    :beforeLabel="$labelBefore"
    :inLabel="$inLabel"
>
    <x-slot:beforeSlot>
        {!! $beforeInner !!}
    </x-slot:beforeSlot>

    {!! $slot !!}

    <x-slot:afterSlot>
        <x-moonshine::form.hint>
            {{ $afterInner }}
        </x-moonshine::form.hint>
    </x-slot:afterSlot>
</x-moonshine::form.input-wrapper>

{!! $after !!}
