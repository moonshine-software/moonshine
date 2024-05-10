@props([
    'form',
])
<div x-id="['has-one']"
     :id="$id('has-one')"
>
    <x-moonshine::layout.line-break />

    {{ $form->render() }}
</div>
