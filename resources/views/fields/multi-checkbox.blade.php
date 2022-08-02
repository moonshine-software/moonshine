<div>
    @foreach($element->values() as $optionValue => $optionName)
        <div>
            <input @checked($element->isChecked($optionValue))
                   id="{{ $element->id($optionValue) }}"
                   type="checkbox" name="{{ $element->name() }}"
                   value="{{ $optionValue }}"
            />

            <label class="ml-5" for="{{ $element->id() }}_{{ $optionValue }}">
                {{ $optionName }}
            </label>

            @if($element->getFields())
                <div id="{{ $element->id($optionValue) }}_pivots">
                    @foreach($element->getFields() as $pivotField)
                        <div class="my-4">
                            {{ $resource->renderField($pivotField, $element->pivotValue($item, $optionValue))}}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($element->getFields())
            <script>
                let input_{{ $element->id() }} = document.querySelector("#{{ $element->id($optionValue) }}");

                let pivotsDiv_input_{{ $element->id() }} = document.querySelector("#{{ $element->id($optionValue) }}_pivots");

                let inputs_{{ $element->id() }} = pivotsDiv_input_{{ $element->id() }}.querySelectorAll('input, textarea, select');

                inputs_{{ $element->id() }}.forEach(function (value, key) {
                    value.addEventListener('input', (event) => {
                        input_{{ $element->id() }}.checked = event.target.value;
                    });
                })
            </script>
        @endif
    @endforeach
</div>
