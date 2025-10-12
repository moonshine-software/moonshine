@props(['color'])
<div class="flex gap-2 items-center">
    <span class="size-4 rounded-sm" style="background-color: {{ $color }}"></span>
    <span> {!! $color ?? '' !!} </span>
</div>
