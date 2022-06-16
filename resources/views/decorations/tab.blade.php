<div x-show="activeTab === '{{ $decoration->id() }}'">
    @foreach($decoration->fields() as $field)
        @if($field instanceof \Leeto\MoonShine\Decorations\Decoration)
            {{ $resource->renderDecoration($field, $item) }}
        @else
            <x-moonshine::field-container :field="$field" :item="$item" :resource="$resource">
                {{ $resource->renderField($field, $item) }}
            </x-moonshine::field-container>
        @endif
    @endforeach
</div>