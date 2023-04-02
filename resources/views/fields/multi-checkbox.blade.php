@if(method_exists($element, 'isTree') && $element->isTree())
    @include('moonshine::fields.tree', [
        'element' => $element,
        'item' => $item,
        'resource' => $resource
    ])
@else

    @if(method_exists($element, 'isOnlySelected') && $element->isOnlySelected())
        <div x-data="search('{{ route('moonshine.search.relations', class_basename($element)) }}', '{{ $resource->uriKey() }}', '{{ $element->field() }}')">
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

            <div class="my-4"></div>

            <template x-for="item in items">
                <div x-data="pivot" x-init="autoCheck">
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
                                {{ $resource->renderComponent(
                                        $pivotField->setAttribute('x-bind:name', '`'.(preg_replace('/\[\]$/', '[${item.key}]', $pivotField->name()).'`')),
                                        $element->pivotValue($item, null)
                                ) }}
                            @endforeach
                        @endif
                    </x-moonshine::form.pivot>
                </div>
            </template>
        </div>
    @endif

    @foreach($element->values() as $optionValue => $optionName)
        <div x-data="pivot" x-init="autoCheck">
            <x-moonshine::form.pivot
                id="{{ $element->id($optionValue) }}"
                name="{{ $element->name($optionValue) }}"
                label="{{ $optionName }}"
                value="{{ $optionValue }}"
                :checked="$element->isChecked($item, $optionValue)"
                :withFields="$element->getFields()->isNotEmpty()"
                :attributes="$element->attributes()"
            >
                @if($element->getFields()->isNotEmpty())
                    @foreach($element->getFields() as $pivotField)
                        {{ $resource->renderComponent(
                                $pivotField->clearXModel()->setName(preg_replace('/\[\]$/', "[$optionValue]", $pivotField->name())),
                                $element->pivotValue($item, $optionValue)
                        ) }}
                    @endforeach
                @endif
            </x-moonshine::form.pivot>
        </div>
    @endforeach
@endif
