@props([
    'component',
    'isCreatable' => false,
    'createButton' => '',
])
<div x-id="['has-many']"
     :id="$id('has-many')"
>
    @if($isCreatable)
        <x-moonshine::layout.divider />

        {!! $createButton !!}
    @endif

    <x-moonshine::layout.divider />

    {{ $component->render() }}
</div>
