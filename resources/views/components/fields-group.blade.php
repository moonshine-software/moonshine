@props([
    'components' => []
])
@foreach($components as $fieldOrDecoration)
    @continue(!isSeeWhenExists($fieldOrDecoration))

    {!! is_field($fieldOrDecoration) ? $fieldOrDecoration->getBeforeRender() : '' !!}
    @if(is_field($fieldOrDecoration) && $fieldOrDecoration->hasWrapper())
        <x-moonshine::field-container :field="$fieldOrDecoration">
            {!!
             $fieldOrDecoration
                    ->{is_field($fieldOrDecoration) && $fieldOrDecoration->isForcePreview()
                        ? 'preview'
                        : 'render'}()
            !!}
        </x-moonshine::field-container>
    @else
        {!! $fieldOrDecoration
                ->{is_field($fieldOrDecoration) && $fieldOrDecoration->isForcePreview()
                    ? 'preview'
                    : 'render'}()
        !!}
    @endif
    {!! is_field($fieldOrDecoration) ? $fieldOrDecoration->getAfterRender() : '' !!}
@endforeach
