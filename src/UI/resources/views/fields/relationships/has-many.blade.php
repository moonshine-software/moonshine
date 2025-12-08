@props([
    'component',
    'isCreatable' => false,
    'createButton' => '',
    'buttons' => '',
])
<div x-id="['has-many']"
     :id="$id('has-many')"
>
    @if($isCreatable || ($buttons && $buttons->getActions()->isNotEmpty()))
        <x-moonshine::layout.flex justify-align="between" items-align="start">
            @if($isCreatable)
                {!! $createButton !!}
            @endif

            {!! $buttons ?? '' !!}
        </x-moonshine::layout.flex>


        <x-moonshine::layout.line-break />
    @endif

    {!! $component !!}
</div>
