@if($element->isAsyncSearch())
    <div x-data="asyncSearch('{{ route('moonshine.search.relations', [
            'column' => $element->column()
        ]) }}')">
        <div class="dropdown">
            <x-moonshine::form.input
                x-model="query"
                @input.debounce="search"
                :placeholder="__('moonshine::ui.search')"
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
            <div x-data="pivot" x-init="autoCheck" class="mt-3">
                <x-moonshine::form.pivot
                    :label="'<span x-text=\'item.value\' />'"
                    x-bind:id="`{{ $element->id('${item.key}') }}`"
                    x-bind:name="`{{ $element->name('${item.key}') }}`"
                    x-bind:value="item.key"
                    :checked="true"
                    :withFields="true"
                    :attributes="$element->attributes()"
                >
                    @if($element->getFields()->isNotEmpty())
                        @foreach($element->getFields() as $pivotField)
                            {{
                                $pivotField
                                    ->setAttribute(
                                        'x-bind:name',
                                        '`'.(preg_replace(
                                                '/\[\]$/',
                                                '[${item.key}]'.($pivotField->attributes()->get('multiple') ? '[]' : ''),
                                                $pivotField->name()).'`'
                                            )
                                    )
                                    ->render()
                            }}
                        @endforeach
                    @endif
                </x-moonshine::form.pivot>
            </div>
        </template>
    </div>
@endif

@foreach($element->values() as $optionValue => $optionName)
    <div x-data="pivot" x-init="autoCheck" class="mt-1 first-of-type:mt-0">
        <x-moonshine::form.pivot
            id="{{ $element->id($optionValue) }}"
            name="{{ $element->name($optionValue) }}"
            label="{{ $optionName }}"
            value="{{ $optionValue }}"
            :checked="$element->isChecked($optionValue)"
            :withFields="$element->getFields()->isNotEmpty()"
            :attributes="$element->attributes()"
        >
            @if($element->getFields()->isNotEmpty())
                @foreach($element->getFields() as $pivotField)
                    {{
                        $pivotField
                            ->clearXModel()
                            ->setName(
                                preg_replace(
                                    '/\[\]$/',
                                    "[$optionValue]".($pivotField->attributes()->get('multiple') ? '[]' : ''),
                                    $pivotField->name()
                                )
                            )
                            ->render()
                    }}
                @endforeach
            @endif
        </x-moonshine::form.pivot>
    </div>
@endforeach
