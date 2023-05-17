@props([
    'adaptiveColSpan' => 12,
    'colSpan' => 12,
])
<div
    {{ $attributes->class(['space-y-6', "col-span-$adaptiveColSpan", "xl:col-span-$colSpan"]) }}
>
    {{ $slot }}
</div>
