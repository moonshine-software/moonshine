<x-moonshine::form.input-extensions
    :extensions="$element->getExtensions()"
>
    @if($element->hasButtons())
        <div class="inline-flex items-center gap-2" x-data="{ number_{{ $element->id() }}: {{$element->value()}} }">
                <x-moonshine::form.button @click="number_{{ $element->id() }}-=number_{{ $element->id() }} > {{$element->min}} ? {{ $element->step }} : 0">
                    <x-moonshine::icon icon="heroicons.minus-small" />
                </x-moonshine::form.button>
    @endif
        <x-moonshine::form.input
            :attributes="$element->attributes()->except('x-on:change')->merge([
            'id' => $element->id(),
            'name' => $element->name(),
        ])"
            @if($element->hasButtons())
                x-model="number_{{ $element->id() }}"
            @endif
            @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
            :@change="(($updateOnPreview ?? false)
                ? 'updateColumn(
                    `'.$element->getUpdateOnPreviewUrl().'`,
                    `'.$element->column().'`
                )'
                : $element->attributes()->get('x-on:change')
            )"
        />
        @if($element->hasButtons())
            <x-moonshine::form.button @click="number_{{ $element->id() }}=number_{{ $element->id() }} + (number_{{ $element->id() }} < {{$element->max}} ? {{ $element->step }} : 0)">
                <x-moonshine::icon icon="heroicons.plus-small" />
            </x-moonshine::form.button>
        </div>
    @endif

</x-moonshine::form.input-extensions>
