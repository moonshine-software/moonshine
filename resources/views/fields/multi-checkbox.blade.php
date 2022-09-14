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
                       id="{{ $field->id($loop->index) }}"
                       type="checkbox" name="{{ $field->name($loop->index) }}"
                       value="{{ $optionValue }}"
                />

                <label class="ml-5" for="{{ $field->id($loop->index) }}">
                    {{ $optionName }}
                </label>

                @if($field->getFields())
                    <div id="{{ $field->id($loop->index) }}_pivots">
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
                    let input_{{ $field->id($loop->index) }} = document.querySelector("#{{ $field->id($loop->index) }}");

                    let pivotsDiv_input_{{ $field->id($loop->index) }} = document.querySelector("#{{ $field->id($loop->index) }}_pivots");

                    let inputs_{{ $field->id($loop->index) }} = pivotsDiv_input_{{ $field->id($loop->index) }}.querySelectorAll('input, textarea, select');

                    inputs_{{ $field->id($loop->index) }}.forEach(function (value, key) {
                        value.addEventListener('input', (event) => {
                            input_{{ $field->id($loop->index) }}.checked = event.target.value;
                        });
                    })
                </script>
            @endif
        @endforeach
    </div>
@endif
