<div>
    @if((isset($value) && !is_null($value)) || (isset($element) && !is_null($element->formViewValue($item))))
        @if((isset($value) && $value) || (isset($element) && $element->formViewValue($item)))
            <x-moonshine::badge color='green'>
                <x-moonshine::icon icon='heroicons.outline.check'/>
            </x-moonshine::badge>
        @else
            <x-moonshine::badge color='red'>
                <x-moonshine::icon icon='heroicons.outline.x-mark'/>
            </x-moonshine::badge>
        @endif
    @endif
</div>
