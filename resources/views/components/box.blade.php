@props([
    'title' => false
])
<div {{ $attributes->class('box') }}>
    @if($title ?? false) <h2 class="box-title">{{ $title }}</h2> @endif

    {{ $slot }}
</div>
