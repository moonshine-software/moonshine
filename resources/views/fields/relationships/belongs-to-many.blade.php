<div x-id="['belongs-to-many']" :id="$id('belongs-to-many')">
    {{
        actionBtn(__('moonshine::ui.add'), to_page($element->getResource(), 'form-page', fragment: 'crud-form'))
            ->inModal(fn() => __('moonshine::ui.add'), fn() => '', async: true)
            ->showInLine()
            ->render()
    }}

    <x-moonshine::divider />

    @if($element->isSelectMode())
        <x-moonshine::form.select
            :attributes="$element->attributes()->merge([
            'id' => $element->id(),
            'placeholder' => $element->label() ?? '',
            'name' => $element->name(),
            'multiple' => true
        ])"
            :nullable="$element->isNullable()"
            :searchable="true"
            @class(['form-invalid' => $errors->{$element->getFormName()}->has($element->name())])
            :value="$element->selectedKeys()"
            :values="$element->values()"
            :asyncRoute="$element->isAsyncSearch() ? $element->asyncSearchUrl($element->getFormName()) : null"
        >
        </x-moonshine::form.select>
    @else
        @if($element->isAsyncSearch())
            <div x-data="asyncSearch('{{ $element->asyncSearchUrl($element->getFormName()) }}')">
                <div class="dropdown">
                    <x-moonshine::form.input
                        x-model="query"
                        @input.debounce="search"
                        :placeholder="trans('moonshine::ui.search')"
                    />
                    <div class="dropdown-body pointer-events-auto visible opacity-100">
                        <div class="dropdown-content">
                            <ul class="dropdown-menu">
                                <template x-for="(item, key) in match">
                                    <li class="dropdown-item">
                                        <a href="#"
                                           class="dropdown-menu-link"
                                           x-text="item"
                                           @click.prevent="select(key)"
                                        />
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <template x-for="item in items">

                </template>
            </div>
        @endif

        <x-moonshine::divider />

        <div x-data="pivot" x-init="autoCheck">
            {{ $element->value(withOld: false)->render() }}
        </div>
    @endif
</div>
