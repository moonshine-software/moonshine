@props([
    'component',
])
<div x-id="['has-one']"
     :id="$id('has-one')"
>
    <x-moonshine::layout.line-break />

    {!! $component->render() !!}
</div>
