<div>
    @if((isset($value) && !is_null($value)) || (isset($element) && !is_null($element->formViewValue($item))))
        @if((isset($value) && $value) || (isset($element) && $element->formViewValue($item)))
            <div class="mx-auto h-2 w-2 rounded-full bg-green-500"></div>
        @else
            <div class="mx-auto h-2 w-2 rounded-full bg-red-500"></div>
        @endif
    @endif
</div>
