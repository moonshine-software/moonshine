@props([
    'form',
])
<div x-id="['has-one']"
     :id="$id('has-one')"
>
    <x-moonshine::layout.divider />

    {{ $form->render() }}
</div>
