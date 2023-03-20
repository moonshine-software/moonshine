@props([
    'title' => false,
    'dark' => false
])
<div {{ $attributes->class(['box', 'box-dark' => $dark]) }}>
    @if($title ?? false) <h2 class="box-title">{{ $title }}</h2> @endif

    {{ $slot }}
</div>
