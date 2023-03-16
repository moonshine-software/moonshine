@props([
    'adaptiveColSpan' => 6,
    'colSpan' => 6,
    'title' => false
])
<div class="col-span-{{ $adaptiveColSpan }} xl:col-span-{{ $colSpan }}">
    <div {{ $attributes->class('box') }}>
        @if($title ?? false) <h2 class="box-title">{{ $title }}</h2> @endif

        {{ $slot }}
    </div>
</div>
