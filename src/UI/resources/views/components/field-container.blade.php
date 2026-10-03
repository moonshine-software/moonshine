{{-- @internal --}}
@props([
    'escapeLabel' => null,
    'label' => '',
    'formName' => '',
    'errors' => [],
    'isBeforeLabel' => false,
    'isInsideLabel' => false,
    'before' => null,
    'after' => null,
    'beforeInner' => null,
    'afterInner' => null,
])
{!! $before !!}

<x-moonshine::form.wrapper
    :label="$label" :escape-label="$escapeLabel"
    :form-name="$formName"
    :attributes="$attributes"
    :beforeLabel="$isBeforeLabel"
    :insideLabel="$isInsideLabel"
    :fieldErrors="$errors"
>
    @if($beforeInner ?? false)
    <x-slot:before>
        {!! $beforeInner !!}
    </x-slot:before>
    @endif

    {!! $slot !!}

    @if($afterInner ?? false)
    <x-slot:after>
        <x-moonshine::form.hint>
            {!! $afterInner !!}
        </x-moonshine::form.hint>
    </x-slot:after>
    @endif
</x-moonshine::form.wrapper>

{!! $after !!}
