@props([
    'component',
    'buttons' => [],
    'values' => [],
    'isNullable' => false,
    'isSearchable' => false,
    'isAsyncSearch' => false,
    'isSelectMode' => false,
    'asyncSearchUrl' => '',
    'isCreatable' => false,
    'createButton' => '',
    'fragmentUrl' => '',
    'relationName' => '',
])
<div x-id="['belongs-to-many']"
     :id="$id('belongs-to-many')"
     data-field-block="{{ $attributes->get('name') }}"
>
    @if($isCreatable)
        {!! $createButton !!}

        <x-moonshine::layout.divider />

        @fragment($relationName)
            <div x-data="fragment('{{ $fragmentUrl }}')"
                 @defineEvent('fragment-updated', $relationName, 'fragmentUpdate')
            >
        @endif
            @if($isSelectMode)
                <x-moonshine::form.select
                    :attributes="$attributes->merge([
                        'multiple' => true
                    ])"
                    :nullable="$isNullable"
                    :searchable="true"
                    :values="$values"
                    :asyncRoute="$isAsyncSearch ? $asyncSearchUrl : null"
                >
                </x-moonshine::form.select>
            @else
                @if($isAsyncSearch)
                    <div x-data="asyncSearch('{{ $asyncSearchUrl }}')">
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
                                                    <div x-show="item?.properties?.image"
                                                         class="zoom-in h-10 w-10 overflow-hidden rounded-md"
                                                    >
                                                        <img class="h-full w-full object-cover"
                                                              :src="item.properties.image"
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

                        <x-moonshine::layout.divider />

                        <div x-data="pivot"
                             x-init="autoCheck"
                             class="pivotTable"
                             data-table-name="{{ $component->getName() }}"
                        >
                            <x-moonshine::action-group
                                class="mb-4"
                                :actions="$buttons"
                            />

                            {!! $component->render() !!}
                        </div>
                    </div>
                @else
                    <div x-data="pivot" x-init="autoCheck">
                        <x-moonshine::action-group
                            class="mb-4"
                            :actions="$buttons"
                        />

                        {!! $component->render() !!}
                    </div>
                @endif
            @endif
        @if($isCreatable)
            </div>
            @endfragment
        @endif
</div>
