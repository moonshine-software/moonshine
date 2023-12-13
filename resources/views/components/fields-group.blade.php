@props([
    'components' => []
])
@foreach($components as $fieldOrDecoration)
    @continue(!isSeeWhenExists($fieldOrDecoration))

    @if(is_field($fieldOrDecoration) && $fieldOrDecoration->hasWrapper())
        <x-moonshine::field-container :field="$fieldOrDecoration">
            {!! is_field($fieldOrDecoration) ? $fieldOrDecoration->getBeforeRender() : '' !!}
            {!!
             $fieldOrDecoration
                    ->{is_field($fieldOrDecoration) && $fieldOrDecoration->isForcePreview()
                        ? 'preview'
                        : 'render'}()
            !!}
            {!! is_field($fieldOrDecoration) ? $fieldOrDecoration->getAfterRender() : '' !!}
        </x-moonshine::field-container>
    @else
        {!! is_field($fieldOrDecoration) ? $fieldOrDecoration->getBeforeRender() : '' !!}
        {!! $fieldOrDecoration
                ->{is_field($fieldOrDecoration) && $fieldOrDecoration->isForcePreview()
                    ? 'preview'
                    : 'render'}()
        !!}
        {!! is_field($fieldOrDecoration) ? $fieldOrDecoration->getAfterRender() : '' !!}
    @endif
@endforeach
