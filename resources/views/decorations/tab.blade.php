<div x-show="activeTab === '{{ $decoration->id() }}'">
    @foreach($decoration->fields() as $field)
        @if($field instanceof \Leeto\MoonShine\Decorations\Decoration)
            {!! $field !!}
        @elseif($field instanceof \Leeto\MoonShine\Fields\Field && $field->showOnForm)
            <x-moonshine::field-container :field="$field">
                {!! $field !!}
            </x-moonshine::field-container>
        @endif
    @endforeach
</div>
