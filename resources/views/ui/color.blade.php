@props(['color'])
<div class="flex gap-2 items-center">
    <span class="w-4 h-4 rounded-sm" style="background-color: {{ $color }}"></span>
    <span> {!! $color ?? '' !!} </span>
</div>
