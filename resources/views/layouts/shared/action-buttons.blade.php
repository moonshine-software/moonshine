@props([
    'actionsButtons' => null,
    'filtersButtons' => null,
    'tagsButtons' => null,
])
<div class="flex flex-col sm:flex-row flex-wrap justify-between gap-4">
    <div class="flex flex-row sm:flex-col sm:order-last">{!! $filtersButtons !!}</div>
    <div class="flex flex-col gap-4">
        {!! $actionsButtons !!}
        {!! $tagsButtons !!}
    </div>
</div>
