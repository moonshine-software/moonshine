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
            'type' => 'checkbox',
            'value' => $value,
        ])"
    />
</x-moonshine::form.input-wrapper>

@if($withFields)
    <div
        id="wrapper_{{ $attributes->get('id') }}_pivots"
        {!! $attributes->get('x-bind:id') ? 'x-bind:id="`wrapper_'.str_replace('`', '', $attributes->get('x-bind:id')).'_pivots`"' : '' !!}
    >
        <x-moonshine::form.input-wrapper
            class="form-group-inline w-full"
        >
            {{ $slot }}
        </x-moonshine::form.input-wrapper>

        @if(!$attributes->get('x-bind:id'))
            <script>
                let input_{{ $attributes->get('id') }} = document.querySelector("#{{ $attributes->get('id') }}");

                let pivotsDiv_input_{{ $attributes->get('id') }} = document.querySelector("#wrapper_{{ $attributes->get('id') }}_pivots");

                let inputs_{{ $attributes->get('id') }} = pivotsDiv_input_{{ $attributes->get('id') }}.querySelectorAll('input, textarea, select');

                inputs_{{ $attributes->get('id') }}.forEach(function (value, key) {
                    value.addEventListener('input', (event) => {
                        input_{{ $attributes->get('id') }}.checked = event.target.value;
                    });
                })
            </script>
        @endif
    </div>
@endif
