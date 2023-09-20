@php
    use MoonShine\Fields\Field;
@endphp

@foreach($components as $fieldOrDecoration)
    @if($fieldOrDecoration instanceof Field && $fieldOrDecoration->hasWrapper())
        <x-moonshine::field-container :field="$fieldOrDecoration">
            {!! $fieldOrDecoration->getBeforeRender() !!}
            {{ $fieldOrDecoration->render() }}
            {!! $fieldOrDecoration->getAfterRender() !!}
        </x-moonshine::field-container>
    @else
        {{ $fieldOrDecoration->render() }}
    @endif
@endforeach
