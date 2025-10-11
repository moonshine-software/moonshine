@props([
    'items' => [],
    'dropdown' => false
])

@if($items !== [])
    <ul {{ $attributes->class(['menu-list' => !$dropdown, 'menu-submenu' => $dropdown]) }}>
        @foreach($items as $item)
            {!! $item !!}
        @endforeach
    </ul>
@endif

{{ $slot ?? '' }}
