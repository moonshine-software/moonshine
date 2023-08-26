<div x-data="{morphType: '{{ $element->value() }}'}">
    <x-moonshine::form.select
        :name="str($element->name())->replace($element->column(), $element->getMorphType())"
        x-model="morphType"
        required="required"
        :values="$element->getTypes()"
    />

    <hr class="divider"/>

    <x-moonshine::form.select
        :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
    ])"
        :nullable="false"
        :searchable="true"
        @class(['form-invalid' => $errors->{$element->getFormName()}->has($element->name())])
        x-bind:data-async-extra="morphType"
        :value="$element->value()"
        :values="$element->values()"
        :asyncRoute="$element->isAsyncSearch() ? $element->asyncSearchUrl($element->getFormName()) : null"
    >
    </x-moonshine::form.select>
</div>
