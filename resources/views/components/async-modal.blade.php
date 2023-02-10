@props([
    'id',
    'route',
])

<span id="button_{{ $id }}"></span>

<template x-teleport="body">
    <x-moonshine::modal wide>
        <div x-data="{}" id="{{ $id }}">
            <div class="moonshine-loader" style="height: 50px; width: 50px;"></div>
        </div>

        <x-slot name="outerHtml">
            <template x-teleport="#button_{{ $id }}">
                <button x-on:click="isOpen = !isOpen; getData_{{ str($id)->snake()->value() }}();"
                        type="button"
                    {{ $attributes }}
                >
                    {{ $slot ?? '' }}
                </button>
            </template>
        </x-slot>
    </x-moonshine::modal>
</template>

<script>
    function getData_{{ str($id)->snake()->value() }}() {
        fetch('{!! str_replace('&amp;', '&', $route) !!}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
        }).then(function (response) {
            return response.text()
        }).then(function (html) {
            let containerElement = document
                .getElementById('{{ $id }}')

            containerElement.innerHTML = html

            const scriptElements = containerElement.querySelectorAll("script");

            Array.from(scriptElements).forEach((scriptElement) => {
                const clonedElement = document.createElement("script");

                Array.from(scriptElement.attributes).forEach((attribute) => {
                    clonedElement.setAttribute(attribute.name, attribute.value);
                });

                clonedElement.text = scriptElement.text;

                scriptElement.parentNode.replaceChild(clonedElement, scriptElement);
            });

        }).catch(function (err) {

        });
    }
</script>
