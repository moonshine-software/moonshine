@props([
    'value' => null,
    'component' => null,
    'componentName' => '',
    'buttons' => [],
    'isNullable' => false,
    'isDeduplicate' => false,
    'isSearchable' => false,
    'isAsyncSearch' => false,
    'isSelectMode' => false,
    'isTreeMode' => false,
    'isHorizontalMode' => false,
    'treeHtml' => '',
    'listHtml' => '',
    'asyncSearchUrl' => '',
    'isCreatable' => false,
    'createButton' => '',
    'fragmentUrl' => '',
    'relationName' => '',
    'translates' => [],
    'settings' => [],
    'plugins' => [],
])
<div x-id="['belongs-to-many']"
     :id="$id('belongs-to-many')"
     data-show-when-field="{{ $attributes->get('name') }}"
     data-validation-field="{{$relationName}}"
>
    @if($isCreatable)
        {!! $createButton !!}

        <x-moonshine::layout.divider />

        @fragment($relationName)
            <div x-data="fragment('{{ $fragmentUrl }}')"
                 @defineEvent('fragment_updated', $relationName, 'fragmentUpdate')
            >
        @endif
            @if($isSelectMode)
                <x-moonshine::form.select
                    :attributes="$attributes->merge([
                        'multiple' => true
                    ])"
                    :nullable="$isNullable"
                    :searchable="$isSearchable"
                    :values="$values"
                    :asyncRoute="$isAsyncSearch ? $asyncSearchUrl : null"
                    :settings="$settings"
                    :plugins="$plugins"
                >
                </x-moonshine::form.select>
            @elseif($isTreeMode)
                <div x-data="belongsToMany" x-init='tree(@json($keys))'>
                    {!! $treeHtml !!}
                </div>
            @elseif($isHorizontalMode)
                <div x-data="belongsToMany" x-init='tree(@json($keys))'>
                    {!! $listHtml !!}
                </div>
            @else
                @if($isAsyncSearch)
                    <div x-data="belongsToMany">
                        <div class="dropdown">
                            <x-moonshine::form.input
                                x-model="query"
                                @input.debounce="search('{{ $asyncSearchUrl }}')"
                                :placeholder="$translates['search']"
                            />
                            <div class="dropdown-body mt-1" :class="{ 'pointer-events-auto visible opacity-100': query.length && match.length }">
                                <div class="dropdown-content">
                                    <ul class="dropdown-menu">
                                        <template x-for="(item, key) in match">
                                            <li class="dropdown-item">
                                                <a href="javascript:void(0);"
                                                   class="dropdown-menu-link flex gap-x-2 items-center"
                                                   @click.prevent="select(item, {{ $isDeduplicate ? 1 : 0}})"
                                                >
                                                    <div x-show="item?.properties?.image"
                                                         class="zoom-in h-10 w-10 overflow-hidden rounded-md"
                                                    >
                                                        <img class="h-full w-full object-cover"
                                                              :src="item.properties.image.src"
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

                        <div x-data="belongsToMany"
                             x-init='pivot(@json($keys))'
                             class="js-pivot-table"
                             data-table-name="{{ $componentName }}"
                        >
                            <x-moonshine::action-group
                                class="mb-4"
                                :actions="$buttons"
                            />

                            {!! $component !!}
                        </div>
                    </div>
                @else
                    <div x-data="belongsToMany" x-init='pivot(@json($keys))'>
                        <x-moonshine::action-group
                            class="mb-4"
                            :actions="$buttons"
                        />

                        {!! $component !!}
                    </div>
                @endif
            @endif
        @if($isCreatable)
            </div>
            @endfragment
        @endif
</div>
