@if(method_exists($element, 'isTree') && $element->isTree())
    @include('moonshine::fields.tree', [
        'element' => $element,
        'item' => $item,
        'resource' => $resource
    ])
@else
    @foreach($element->values() as $optionValue => $optionName)
        <x-moonshine::form.input-wrapper
            id="{{ $element->id($loop->index) }}"
            name="{{ $element->id($loop->index) }}"
            class="form-group-inline !m-0"
            label="{{ $optionName }}"
            :beforeLabel="true"
            :inLabel="false"
        >
            <x-moonshine::form.input
                :attributes="$element->attributes()->merge([
                    'id' => $element->id($loop->index),
                    'name' => $element->name($loop->index),
                    'type' => 'checkbox',
                    'value' => $optionValue,
                    'checked' => $element->isChecked($item, $optionValue)
                ])"
            />
        </x-moonshine::form.input-wrapper>

        @if($element->getFields()->isNotEmpty())
            <x-moonshine::form.input-wrapper
                id="{{ $element->id($loop->index) }}_pivots"
                class="form-group-inline w-full"
            >
                @foreach($element->getFields() as $pivotField)
                    {{ $resource->renderComponent($pivotField, $element->pivotValue($item, $optionValue))}}
                @endforeach
            </x-moonshine::form.input-wrapper>

            <script>
                let input_{{ $element->id($loop->index) }} = document.querySelector("#{{ $element->id($loop->index) }}");

                let pivotsDiv_input_{{ $element->id($loop->index) }} = document.querySelector("#wrapper_{{ $element->id($loop->index) }}_pivots");

                let inputs_{{ $element->id($loop->index) }} = pivotsDiv_input_{{ $element->id($loop->index) }}.querySelectorAll('input, textarea, select');

                inputs_{{ $element->id($loop->index) }}.forEach(function (value, key) {
                    value.addEventListener('input', (event) => {
                        input_{{ $element->id($loop->index) }}.checked = event.target.value;
                    });
                })
            </script>
        @endif
    @endforeach
@endif
