@props([
    'rows',
    'actions',
    'colSpan',
    'adaptivecolSpan',
    'overlay' => false,
])
<x-moonshine::grid>
    @foreach($rows as $fields)
        <x-moonshine::column
            :colSpan="$colSpan"
            :adaptiveColSpan="$adaptivecolSpan"
        >
            <x-moonshine::card
                url="#"
                :overlay="$overlay"
                :thumbnail="$fields->first(fn($field) => $field->label() === 'thumbnail')?->getFullPathValues()[0]"
                :title="$fields->first(fn($field) => $field->label() === 'title')?->preview()"
                :subtitle="$fields->first(fn($field) => $field->label() === 'subTitle')?->preview()"
                :values="$fields->filter(fn($field) => !in_array($field->label(), ['badge', 'thumbnail', 'title', 'subTitle']))->mapWithKeys(fn($field) => [$field->label() => $field->value()])"
            >
                <x-slot:header>
                    {!! $fields->first(fn($field) => $field->label() === 'badge')?->preview() !!}
                </x-slot:header>
                {{-- d --}}
                <x-slot:actions>
                    {{-- @foreach($element->getActions($item) as $action)
                        {{ $action->render() }}
                    @endforeach --}}
                </x-slot:actions>
            </x-moonshine::card>
        </x-moonshine::column>
    @endforeach
</x-moonshine::grid>

