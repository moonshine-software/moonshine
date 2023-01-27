@props([
    'id',
    'route',
])
<x-moonshine::modal :wide="true">
    <div x-data="{}" id="{{ $id }}">
        <div class="moonshine-loader" style="height: 50px; width: 50px;"></div>
    </div>

    <x-slot name="outerHtml">
        <button x-on:click="isOpen = !isOpen; getData_{{ $id }}();"
                type="button"
            {{ $attributes }}
        >
            {{ $slot ?? '' }}
        </button>

        <script>
            function getData_{{ $id }}() {
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
    </x-slot>
</x-moonshine::modal>
