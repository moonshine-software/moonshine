@props([
    'component',
    'isCreatable' => false,
    'createButton' => '',
])
<div x-id="['has-many']"
     :id="$id('has-many')"
>
    @if($isCreatable)
        {!! $createButton !!}
    @endif

    <x-moonshine::layout.line-break />

    {!! $component->render() !!}
</div>
