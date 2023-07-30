@php
    use MoonShine\Fields\Field;
@endphp

@foreach($components as $fieldOrDecoration)
    @if($fieldOrDecoration instanceof Field && $fieldOrDecoration->hasFieldContainer())
        <x-moonshine::field-container :field="$fieldOrDecoration">
            {!! $fieldOrDecoration !!}
        </x-moonshine::field-container>
    @else
        {!! $fieldOrDecoration !!}
    @endif
@endforeach
