@if(method_exists($field, 'isTree') && $field->isTree())
    @include('moonshine::fields.tree', [
        'field' => $field,
        'item' => $item,
        'resource' => $resource
    ])
@else
    @foreach($field->values() as $optionValue => $optionName)
        <x-moonshine::form.input-wrapper
            id="{{ $field->id($loop->index) }}"
            name="{{ $field->id($loop->index) }}"
            class="form-group-inline !m-0"
            label="{{ $optionName }}"
            :beforeLabel="true"
            :inLabel="false"
        >
            <x-moonshine::form.input
                :attributes="$field->attributes()->merge([
                    'id' => $field->id($loop->index),
                    'name' => $field->name($loop->index),
                    'type' => 'checkbox',
                    'value' => $optionValue,
                    'checked' => $field->isChecked($item, $optionValue)
                ])"
            />
        </x-moonshine::form.input-wrapper>

        @if($field->getFields()->isNotEmpty())
            <x-moonshine::form.input-wrapper
                id="{{ $field->id($loop->index) }}_pivots"
                class="form-group-inline w-full"
            >
                @foreach($field->getFields() as $pivotField)
                    {{ $resource->renderField($pivotField, $field->pivotValue($item, $optionValue))}}
                @endforeach
            </x-moonshine::form.input-wrapper>

            <script>
                let input_{{ $field->id($loop->index) }} = document.querySelector("#{{ $field->id($loop->index) }}");

                let pivotsDiv_input_{{ $field->id($loop->index) }} = document.querySelector("#wrapper_{{ $field->id($loop->index) }}_pivots");

                let inputs_{{ $field->id($loop->index) }} = pivotsDiv_input_{{ $field->id($loop->index) }}.querySelectorAll('input, textarea, select');

                inputs_{{ $field->id($loop->index) }}.forEach(function (value, key) {
                    value.addEventListener('input', (event) => {
                        input_{{ $field->id($loop->index) }}.checked = event.target.value;
                    });
                })
            </script>
        @endif
    @endforeach
@endif
