@props([
    'value',
    'translates' => [],
])
<button @click.prevent="copy('{{ $value }}')"
        {{ $attributes->class(['expansion']) }}
        type="button"
        x-data="tooltip('{{ $translates['copied'] }}', {placement: 'top', trigger: 'click', delay: [0, 800]})"
>
    <x-moonshine::icon
        icon="clipboard"
        size="4"
    />
</button>
