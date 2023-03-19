@props([
    'adaptiveColSpan' => 12,
    'colSpan' => 12,
])
<div class="col-span-{{ $adaptiveColSpan }} xl:col-span-{{ $colSpan }}">
    {{ $slot }}
</div>
