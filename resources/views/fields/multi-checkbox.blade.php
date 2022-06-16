@if(method_exists($field, 'isTree') && $field->isTree())
    @include('moonshine::fields.tree', [
        'field' => $field,
        'item' => $item,
        'resource' => $resource
    ])
@else
<div>
    @foreach($field->values() as $optionValue => $optionName)
        <div>
            <input @checked($field->isChecked($item, $optionValue))
                id="{{ $field->id($optionValue) }}"
                       type="checkbox" name="{{ $field->name() }}"
                       value="{{ $optionValue }}"
            />

            <label class="ml-5" for="{{ $field->id() }}_{{ $optionValue }}">
                {{ $optionName }}
            </label>

            @if($field->getFields())
                <div id="{{ $field->id($optionValue) }}_pivots">
                    @foreach($field->getFields() as $pivotField)
                        <div class="my-4">
                            {{ $resource->renderField($pivotField, $field->pivotValue($item, $optionValue))}}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($field->getFields())
            <script>
                let input_{{ $field->id() }} = document.querySelector("#{{ $field->id($optionValue) }}");

                let pivotsDiv_input_{{ $field->id() }} = document.querySelector("#{{ $field->id($optionValue) }}_pivots");

                let inputs_{{ $field->id() }} = pivotsDiv_input_{{ $field->id() }}.querySelectorAll('input, textarea, select');

                inputs_{{ $field->id() }}.forEach(function(value, key) {
                  value.addEventListener('input', (event) => {
                    input_{{ $field->id() }}.checked = event.target.value;
                  });
                })
            </script>
        @endif
    @endforeach
</div>
@endif
