<div>
    @if((isset($value) && !is_null($value)) || (isset($element) && !is_null($element->formViewValue($item))))
        @if((isset($value) && $value) || (isset($element) && $element->formViewValue($item)))
            @include('moonshine::ui.badge', [
                                'color' => 'green',
                                'value' => view('moonshine::ui.icons.heroicons.outline.check')->render()
                            ])
        @else
            @include('moonshine::ui.badge', [
                                'color' => 'red',
                                'value' => view('moonshine::ui.icons.heroicons.outline.x-mark')->render()
                            ])
        @endif
    @endif
</div>
