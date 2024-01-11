<div x-id="['belongs-to-many']" :id="$id('belongs-to-many')" data-field-block="{{ $element->column() }}">
    @if($element->isCreatable())
        {!! $element->createButton() !!}

        <x-moonshine::divider />

        @fragment($element->getRelationName())
            <div x-data="fragment('{{ $element->fragmentUrl() }}')"
                 @defineEvent('fragment-updated', $element->getRelationName(), 'fragmentUpdate')
            >
        @endif
            @if($element->isSelectMode())
                <x-moonshine::form.select
                    :attributes="$element->attributes()->merge([
                    'id' => $element->id(),
                    'name' => $element->name(),
                    'multiple' => true
                ])"
                    :nullable="$element->isNullable()"
                    :searchable="true"
                    @class(['form-invalid' => $errors->{$element->getFormName()}->has($element->name())])
                    :value="$element->selectedKeys()"
                    :values="$element->values()"
                    :customProperties="$element->valuesWithProperties(onlyCustom: true)"
                    :asyncRoute="$element->isAsyncSearch() ? $element->asyncSearchUrl() : null"
                >
                </x-moonshine::form.select>
            @else
                @if($element->isAsyncSearch())
                    <div x-data="asyncSearch('{{ $element->asyncSearchUrl() }}')">
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
                                                   class="dropdown-menu-link flex gap-x-2 items-center"
                                                   @click.prevent="select(item)"
                                                >
                                                    <div x-show="item?.customProperties?.image"
                                                         class="zoom-in h-10 w-10 overflow-hidden rounded-md"
                                                    >
                                                        <img class="h-full w-full object-cover"
                                                              :src="item.customProperties.image"
                                                              alt=""
                                                        >
                                                    </div>
                                                    <span x-text="item.label" />
                                                </a>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <x-moonshine::divider />

                        <div x-data="pivot"
                             x-init="autoCheck"
                             class="pivotTable"
                             data-table-name="{{ $element->getTableComponentName() }}"
                        >
                            <x-moonshine::action-group
                                class="mb-4"
                                :actions="$element->getButtons()"
                            />

                            {{ $element->value(withOld: false)->render() }}
                        </div>
                    </div>
                @else
                    <div x-data="pivot" x-init="autoCheck">
                        <x-moonshine::action-group
                            class="mb-4"
                            :actions="$element->getButtons()"
                        />

                        {{ $element->value(withOld: false)->render() }}
                    </div>
                @endif
            @endif
        @if($element->isCreatable())
            </div>
            @endfragment
        @endif
</div>
