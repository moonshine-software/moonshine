<div x-show="activeTab === '{{ $decoration->id() }}'">
    @foreach($decoration->fields() as $field)
        @if($field instanceof \Leeto\MoonShine\Decorations\Decoration)
            {{ $resource->renderDecoration($field, $item) }}
        @elseif($field instanceof \Leeto\MoonShine\Fields\Field && $field->showOnForm)
            <x-moonshine::field-container :field="$field" :item="$item" :resource="$resource">
                {{ $resource->renderField($field, $item) }}
            </x-moonshine::field-container>
        @endif
    @endforeach
</div>