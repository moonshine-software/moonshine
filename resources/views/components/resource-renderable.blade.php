@php
    use MoonShine\Filters\Filter;
    use MoonShine\Fields\Field;
    use MoonShine\Decorations\Decoration;
@endphp
@foreach($components as $fieldOrDecoration)
    @if($fieldOrDecoration instanceof Decoration)
        {{ $resource->renderComponent($fieldOrDecoration, $item) }}
    @elseif(($fieldOrDecoration instanceof Filter && $fieldOrDecoration->isSee($item))
        || ($fieldOrDecoration instanceof Field && $fieldOrDecoration->canDisplayOnForm($item)
                && !$fieldOrDecoration->isResourceModeField())
    )
        @if($fieldOrDecoration->hasFieldContainer())
            <x-moonshine::field-container :field="$fieldOrDecoration" :item="$item" :resource="$resource">
                {{ $resource->renderComponent($fieldOrDecoration, $item) }}
            </x-moonshine::field-container>
        @else
            {{ $resource->renderComponent($fieldOrDecoration, $item) }}
        @endif
    @endif
@endforeach
