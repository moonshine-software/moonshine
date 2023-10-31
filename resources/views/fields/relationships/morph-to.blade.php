<div x-data="{morphType: '{{ $element->typeValue() }}'}" class="flex items-center gap-x-2">
    <div class="sm:w-1/4 w-full">
        <x-moonshine::form.select
            :name="str($element->name())->replace($element->column(), $element->getMorphType())"
            x-model="morphType"
            required="required"
            :value="$element->typeValue()"
            :values="$element->getTypes()"
        />
    </div>

    <div class="sm:w-3/4 w-full">
        <x-moonshine::form.select
            :attributes="$element->attributes()->merge([
                'id' => $element->id(),
                'name' => $element->name(),
            ])"
            :nullable="$element->isNullable()"
            :searchable="true"
            @class(['form-invalid' => $errors->{$element->getFormName()}->has($element->name())])
            x-bind:data-async-extra="morphType"
            :value="$element->value()"
            :values="$element->values()"
            :asyncRoute="$element->isAsyncSearch() ? $element->asyncSearchUrl() : null"
        >
        </x-moonshine::form.select>
    </div>

</div>
