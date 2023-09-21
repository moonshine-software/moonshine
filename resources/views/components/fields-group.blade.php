@foreach($components as $fieldOrDecoration)
    @if(is_field($fieldOrDecoration) && $fieldOrDecoration->hasWrapper())
        <x-moonshine::field-container :field="$fieldOrDecoration">
            {!! $fieldOrDecoration->getBeforeRender() !!}
            {{ $fieldOrDecoration->render() }}
            {!! $fieldOrDecoration->getAfterRender() !!}
        </x-moonshine::field-container>
    @else
        {{ $fieldOrDecoration->render() }}
    @endif
@endforeach
