<div {{ $element->attributes() }} class="flex gap-2 items-center">
    <span class="w-4 h-4 rounded-sm" style="background-color: {{ $element->value() }}"></span>
    <span> {!! $element->value() ?? '' !!} </span>
</div>
