<div
    x-data="code('{{ $element->id() }}', {
        lineNumbers: {{ $element->lineNumbers ? 'true' : 'false' }},
        language: '{{ $element->language ?? 'js' }}',
        readonly: {{ $element->isReadonly() ? 'true' : 'false' }},
    })"
    class="w-100 min-h-[300px] relative border">
</div>

<x-moonshine::form.input
    type="hidden"
    id="{{ $element->id() }}"
    name="{{ $element->name() }}"
    :attributes="$element->attributes()->only(['x-bind:name'])"
    value="{{ $element->formViewValue($item) ?? '' }}"
/>
