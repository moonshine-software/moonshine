<x-moonshine::form.input-extensions
    :extensions="$element->getExtensions()"
>
    <div class="@if($element->withButtons()) inline-flex items-center gap-2 @endif" x-data="{ number_{{ $element->id() }}: {{$element->value()}} }">
        @if($element->withButtons())
            <x-moonshine::form.button @click="number_{{ $element->id() }}-=number_{{ $element->id() }} > {{$element->min}} ? {{ $element->step }} : 0">
                <x-moonshine::icon icon="heroicons.minus-small" />
            </x-moonshine::form.button>
        @endif
        <x-moonshine::form.input
            :attributes="$element->attributes()->except('x-on:change')->merge([
            'id' => $element->id(),
            'name' => $element->name(),
        ])"
            x-model="number_{{ $element->id() }}"
            @class(['form-invalid' => formErrors($errors, $element->getFormName())->has($element->name())])
            :@change="(($updateOnPreview ?? false)
                ? 'updateColumn(
                    `'.$element->getUpdateOnPreviewUrl().'`,
                    `'.$element->column().'`
                )'
                : $element->attributes()->get('x-on:change')
            )"
        />
        @if($element->withButtons())
            <x-moonshine::form.button @click="number_{{ $element->id() }}=number_{{ $element->id() }} + (number_{{ $element->id() }} < {{$element->max}} ? {{ $element->step }} : 0)">
                <x-moonshine::icon icon="heroicons.plus-small" />
            </x-moonshine::form.button>
        @endif
    </div>

</x-moonshine::form.input-extensions>
