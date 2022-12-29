@if($field->isResourceMode())
    @if($item->exists)
        <div id="has_many_{{ $field->id() }}"></div>

        <script>
            fetch('{{ $field->resource()->route('index') }}?related_column={{ $item->{$field->relation()}()->getForeignKeyName() }}&related_key={{ $item->getKey() }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
            }).then(function (response) {
                return response.text()
            }).then(function (html) {
                let containerElement = document
                    .getElementById('has_many_{{ $field->id() }}')

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
        </script>
    @endif
@else
    @include('moonshine::fields.shared.'.($field->isFullPage() ? 'full' : 'table').'-fields', [
        'field' => $field,
        'resource' => $resource,
        'item' => $item,
        'model' => $field->formViewValue($item)->first() ?? $field->getRelated($item),
        'level' => $level ?? 0
    ])
@endif
